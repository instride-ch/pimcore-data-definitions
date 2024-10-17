<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Controller;

use Instride\Bundle\DataDefinitionsBundle\Form\Type\ImportRulesImportType;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Pimcore\Bundle\AdminBundle\Controller\GDPR\AdminController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ImportRuleController extends AdminController
{
    public function importAction(Request $request, FormFactoryInterface $formFactory): JsonResponse
    {
        $form = $formFactory->createNamed('', ImportRulesImportType::class);

        $form = $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->json(['success' => false]);
        }

        $data = $form->getData();
        $file = $data['file'];

        if (!$file instanceof UploadedFile) {
            return $this->json(['success' => false]);
        }

        $file = $file->move(PIMCORE_SYSTEM_TEMP_DIRECTORY);

        $reader = $this->getXlsxReader();
        $reader->open($file->getPathname());
        $sheetIterator = $reader->getSheetIterator();
        $sheetIterator->rewind();
        $rowIterator = $sheetIterator->current()->getRowIterator();

        $headers = null;
        $headersCount = null;
        $raw = [];
        $rules = [];

        foreach ($rowIterator as $row) {
            if (null === $headers) {
                $headers = $row;
                $headersCount = count($headers);

                continue;
            }

            $rowCount = count($row);
            if ($rowCount < $headersCount) {
                // append missing values
                $row = array_pad($row, $headersCount, null);
            } elseif ($rowCount >= $headersCount) {
                // remove overflow
                $row = array_slice($row, 0, $headersCount);
            }

            $raw[] = array_combine($headers, $row);
        }

        foreach ($raw as $rawRule) {
            $rule = [
                'name' => $rawRule['name'],
                'active' => strtolower($rawRule['active']) === 'yes',
            ];
            $conditions = [];
            $actions = [];
            //new type
            //same config key

            $condition = null;
            $action = null;

            $lastConfigType = null;
            $lastConfigKey = null;
            $lastConfigIndex = null;

            foreach ($rawRule as $key => $value) {
                if (str_starts_with($key, 'condition_')) {
                    if (!$value) {
                        continue;
                    }

                    $configType = substr($key, strlen('condition_'));
                    $configType = substr($configType, 0, strpos($configType, '___'));

                    $configKeyAndIndex = substr($key, strpos($key, '___') + 3);
                    [$configIndex, $configKey] = explode('___', $configKeyAndIndex);

                    if (null === $lastConfigType && null === $lastConfigKey && null === $lastConfigIndex) {
                        $lastConfigType = $configType;
                        $lastConfigKey = $configKey;
                        $lastConfigIndex = $configIndex;
                        $condition = [
                            'type' => $configType,
                            'configuration' => [],
                        ];
                    } else {
                        if ($lastConfigType !== $configType || $lastConfigKey === $configKey || $lastConfigIndex !== $configIndex) {
                            if (null !== $condition && count($condition['configuration']) > 0) {
                                $conditions[] = $condition;
                            }

                            $condition = [
                                'type' => $configType,
                                'configuration' => [],
                            ];
                        }
                    }

                    $condition['configuration'][$configKey] = $value;
                }

                if (str_starts_with($key, 'action_')) {
                    if (!$value) {
                        continue;
                    }

                    $configType = substr($key, strlen('action_'));
                    $configType = substr($configType, 0, strpos($configType, '___'));

                    $configKeyAndIndex = substr($key, strpos($key, '___') + 3);
                    [$configIndex, $configKey] = explode('___', $configKeyAndIndex);

                    if (null === $lastConfigType && null === $lastConfigKey && null === $lastConfigIndex) {
                        $lastConfigType = $configType;
                        $lastConfigKey = $configKey;
                        $lastConfigIndex = $configIndex;
                        $action = [
                            'type' => $configType,
                            'configuration' => [],
                        ];
                    } else {
                        if ($lastConfigType !== $configType || $lastConfigKey === $configKey || $lastConfigIndex !== $configIndex) {
                            if (null !== $action && count($action['configuration']) > 0) {
                                $actions[] = $action;
                            }

                            $action = [
                                'type' => $configType,
                                'configuration' => [],
                            ];
                        }
                    }

                    $action['configuration'][$configKey] = $value;
                }
            }

            if ($condition !== null && count($condition['configuration']) > 0) {
                $conditions[] = $condition;
            }

            if ($action !== null && count($action['configuration']) > 0) {
                $actions[] = $action;
            }

            $rule['actions'] = $actions;
            $rule['conditions'] = $conditions;
            $rule['id'] = sprintf(
                '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
                random_int(0, 65535),
                random_int(0, 65535),
                random_int(0, 65535),
                random_int(16384, 20479),
                random_int(32768, 49151),
                random_int(0, 65535),
                random_int(0, 65535),
                random_int(0, 65535),
            );

            $rules[] = $rule;
        }

        return $this->json(['success' => true, 'rules' => $rules]);
    }

    public function exportAction(Request $request): BinaryFileResponse
    {
        $rules = json_decode($request->get('rules', '[]'), true);

        $result = [];

        $filePath = tempnam(sys_get_temp_dir(), 'import_rule_set');

        $writer = $this->getXlsxWriter();
        $writer->openToFile($filePath);

        $headers = [
            'name',
            'active',
        ];
        $headersCondition = [];
        $headersAction = [];

        //Determine the headers first
        foreach ($rules as $rule) {
            $countPerType = [];

            foreach ($rule['conditions'] as $condition) {
                $type = $condition['type'];

                foreach ($condition['configuration'] as $key => $value) {
                    $conditionHeader = 'condition_' . $type . '___1___' . $key;

                    if (!array_key_exists($conditionHeader, $countPerType)) {
                        $countPerType[$conditionHeader] = 0;
                    }

                    ++$countPerType[$conditionHeader];

                    if ($countPerType[$conditionHeader] > 1) {
                        $conditionHeader = 'condition_' . $type . '___' . $countPerType[$conditionHeader] . '___' . $key;
                    }

                    if (!in_array($conditionHeader, $headersCondition)) {
                        $headersCondition[] = $conditionHeader;
                    }
                }
            }

            foreach ($rule['actions'] as $action) {
                $type = $action['type'];

                foreach ($action['configuration'] as $key => $value) {
                    $actionHeader = 'action_' . $type . '___1___' . $key;

                    if (!array_key_exists($actionHeader, $countPerType)) {
                        $countPerType[$actionHeader] = 0;
                    }

                    ++$countPerType[$actionHeader];

                    if ($countPerType[$actionHeader] > 1) {
                        $actionHeader = 'action_' . $type . '___' . $countPerType[$actionHeader] . '___' . $key;
                    }

                    if (!in_array($actionHeader, $headersAction)) {
                        $headersAction[] = $actionHeader;
                    }
                }
            }
        }

        //prepare the data
        foreach ($rules as $rule) {
            $countPerType = [];
            $entry = [
                'name' => $rule['name'],
                'active' => $rule['active'] ? 'yes' : 'no',
            ];
            $conditions = [];
            $actions = [];

            foreach ($rule['conditions'] as $condition) {
                $type = $condition['type'];

                foreach ($condition['configuration'] as $key => $value) {
                    $conditionHeader = 'condition_' . $type . '___1___' . $key;

                    if (!array_key_exists($conditionHeader, $countPerType)) {
                        $countPerType[$conditionHeader] = 0;
                    }

                    ++$countPerType[$conditionHeader];

                    if ($countPerType[$conditionHeader] > 1) {
                        $conditionHeader = 'condition_' . $type . '___' . $countPerType[$conditionHeader] . '___' . $key;
                    }

                    if (is_array($value)) {
                        $value = json_encode($value);
                    }

                    $conditions[$conditionHeader] = $value;
                }
            }

            foreach ($rule['actions'] as $action) {
                $type = $action['type'];

                foreach ($action['configuration'] as $key => $value) {
                    $actionHeader = 'action_' . $type . '___1___' . $key;

                    if (!array_key_exists($actionHeader, $countPerType)) {
                        $countPerType[$actionHeader] = 0;
                    }

                    ++$countPerType[$actionHeader];

                    if ($countPerType[$actionHeader] > 1) {
                        $actionHeader = 'action_' . $type . '___' . $countPerType[$actionHeader] . '___' . $key;
                    }

                    if (is_array($value)) {
                        $value = json_encode($value);
                    }

                    $actions[$actionHeader] = $value;
                }
            }

            //Fill empty headers
            foreach ($headersCondition as $header) {
                $entry[$header] = '';
            }

            foreach ($headersAction as $header) {
                $entry[$header] = '';
            }

            foreach ($conditions as $key => $value) {
                $entry[$key] = $value;
            }

            foreach ($actions as $key => $value) {
                $entry[$key] = $value;
            }

            $result[] = Row::fromValues($entry);
        }

        //merge all headers
        $headerValues = array_values(array_merge($headers, $headersCondition, $headersAction));
        $headers = Row::fromValues($headerValues);

        //write data
        $writer->addRow($headers);
        $writer->addRows($result);
        $writer->close();

        return new BinaryFileResponse($filePath);
    }

    protected function getXlsxReader(): Reader
    {
        return new Reader();
    }

    protected function getXlsxWriter(): Writer
    {
        return new Writer();
    }
}
