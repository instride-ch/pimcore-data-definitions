<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Controller;

use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Wvision\Bundle\DataDefinitionsBundle\Form\Type\ImportRulesImportType;

class ImportRuleController extends AdminController
{
    public function importAction(Request $request, FormFactoryInterface $formFactory)
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

        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($file->getPathname());
        $sheetIterator = $reader->getSheetIterator();
        $sheetIterator->rewind();
        $rowIterator = $sheetIterator->current()->getRowIterator();

        $headers = null;
        $headersCount = null;
        $raw = [];

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
                'active' => $rawRule['active'] === 'yes',
            ];
            $conditions = [];

            //new type
            //same config key

            $condition = null;
            $action = null;

            $configuration = [];
            $lastConfigType = null;
            $lastConfigKey = null;
            $lastConfigIndex = null;

            foreach ($rawRule as $key => $value) {
                if (strpos($key, 'condition_') === 0) {
                    if (!$value) {
                        continue;
                    }

                    $configType = substr($key, strlen('condition_'));
                    $configType = substr($configType, 0, strpos($configType, '___'));

                    $configKeyAndIndex = substr($key, strpos($key, '___') + 3);
                    list($configIndex, $configKey) = explode('___', $configKeyAndIndex);

                    if (null === $lastConfigType && null === $lastConfigKey && null === $lastConfigIndex) {
                        $lastConfigType = $lastConfigType;
                        $lastConfigKey = $configKey;
                        $condition = [
                            'type' => $configType,
                            'configuration' => []
                        ];
                    }
                    else {
                        if ($lastConfigType !== $configType || $lastConfigKey === $configKey || $lastConfigIndex !== $configIndex) {
                            if (null !== $condition && count($condition['configuration']) > 0) {
                                $conditions[] = $condition;
                            }

                            $condition = [
                                'type' => $configType,
                                'configuration' => []
                            ];
                        }
                    }

                    $condition['configuration'][$configKey] = $value;
                }

                if (strpos($key, 'action_') === 0) {
                    if (!$value) {
                        continue;
                    }

                    $configType = substr($key, strlen('action_'));
                    $configType = substr($configType, 0, strpos($configType, '___'));

                    $configKeyAndIndex = substr($key, strpos($key, '___') + 3);
                    list($configIndex, $configKey) = explode('___', $configKeyAndIndex);

                    if (null === $lastConfigType && null === $lastConfigKey && null === $lastConfigIndex) {
                        $lastConfigType = $lastConfigType;
                        $lastConfigKey = $configKey;
                        $action = [
                            'type' => $configType,
                            'configuration' => []
                        ];
                    }
                    else {
                        if ($lastConfigType !== $configType || $lastConfigKey === $configKey || $lastConfigIndex !== $configIndex) {
                            if (null !== $action && count($action['configuration']) > 0) {
                                $actions[] = $action;
                            }

                            $action = [
                                'type' => $configType,
                                'configuration' => []
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

            $rules[] = $rule;
        }

        return $this->json(['success' => true, 'rules' => $rules]);
    }

    public function exportAction(Request $request)
    {
        $rules = json_decode($request->get('rules', '[]'), true);

        $result = [];

        $filePath = tempnam(sys_get_temp_dir(), 'import_rule_set');

        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($filePath);

        $headers = [
            'name',
            'active'
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

                    $countPerType[$conditionHeader]++;

                    if ($countPerType[$conditionHeader] > 1) {
                        $conditionHeader = 'condition_' . $type . '___'.$countPerType[$conditionHeader].'___' . $key;
                    }

                    if (!in_array($conditionHeader, $headersCondition)) {
                        $headersCondition[] = $conditionHeader;
                    }
                }
            }

            foreach ($rule['actions'] as $action) {
                $type = $action['type'];

                foreach ($action['configuration'] as $key => $value) {
                    $actionHeader = 'action_' . $type . '___' . $key;

                    if (!array_key_exists($conditionHeader, $countPerType)) {
                        $countPerType[$actionHeader] = 0;
                    }

                    $countPerType[$actionHeader]++;

                    if ($countPerType[$conditionHeader] > 1) {
                        $actionHeader = 'action_' . $type . '___'.$countPerType[$conditionHeader].'___' . $key;
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
            $sameConditionsOfType = [];
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

                    $countPerType[$conditionHeader]++;

                    if ($countPerType[$conditionHeader] > 1) {
                        $conditionHeader = 'condition_' . $type . '___'.$countPerType[$conditionHeader].'___' . $key;
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
                    $actionHeader = 'action_' . $type . '___' . $key;

                    if (!array_key_exists($actionHeader, $countPerType)) {
                        $countPerType[$actionHeader] = 0;
                    }

                    $countPerType[$actionHeader]++;

                    if ($countPerType[$actionHeader] > 1) {
                        $actionHeader = 'action_' . $type . '___'.$countPerType[$conditionHeader].'___' . $key;
                    }

                    if (is_array($value)) {
                        $value = json_encode($value);
                    }

                    $actions[$actionHeader] = $value;
                }
            }

            $exportActions = [];
            $exportConditions = [];

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

            $entry = array_merge($entry, $exportConditions, $exportActions);
            $result[] = $entry;
        }

        //merge all headers
        $headers = array_values(array_merge($headers, $headersCondition, $headersAction));

        //write data
        $writer->addRow($headers);
        $writer->addRows($result);
        $writer->close();

        return new BinaryFileResponse($filePath);
    }
}

