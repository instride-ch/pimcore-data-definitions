<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use ImportDefinitionsBundle\Model\DefinitionInterface;
use ImportDefinitionsBundle\Model\Mapping\FromColumn;
use Pimcore\Model\DataObject;
use ImportDefinitionsBundle\Model\Mapping\ToColumn;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefinitionController extends ResourceController
{
    /**
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getConfigAction(Request $request)
    {
        $providers = $this->getConfigProviders();
        $interpreters = $this->getConfigInterpreters();
        $cleaners = $this->getConfigCleaners();
        $setters = $this->getConfigSetters();
        $filters = $this->getConfigFilters();
        $runners = $this->getConfigRunners();

        return $this->viewHandler->handle([
            'providers' => array_keys($providers),
            'interpreter' => array_keys($interpreters),
            'cleaner' => array_keys($cleaners),
            'setter' => array_keys($setters),
            'filters' => array_keys($filters),
            'runner' => array_keys($runners)
        ]);
    }

    public function testDataAction(Request $request)
    {
        $id = $request->get("id");
        $definition = $this->repository->find($id);

        if ($definition instanceof DefinitionInterface) {
            try {
                if ($this->get('import_definition.registry.provider')->get($definition->getProvider())->testData($definition->getConfiguration())) {
                    return $this->viewHandler->handle(['success' => true]);
                }
            } catch (\Exception $ex) {
                return $this->viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    /**
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getColumnsAction(Request $request)
    {
        $id = $request->get('id');
        $definition = $this->repository->find($id);

        if ($definition instanceof DefinitionInterface && $definition->getClass()) {
            $customFromColumn = new FromColumn();
            $customFromColumn->setIdentifier('custom');
            $customFromColumn->setLabel('Custom');

            try {
                $fromColumns = $this->get('import_definition.registry.provider')->get($definition->getProvider())->getColumns($definition->getConfiguration());
                $fromColumns[] = $customFromColumn;
            } catch (\Exception $e) {
                $fromColumns = [];
            }

            $toColumns = $this->getClassDefinitionForFieldSelection(DataObject\ClassDefinition::getByName($definition->getClass()));
            $mappings = $definition->getMapping();
            $mappingDefinition = [];
            $fromColumnsResult = [];

            foreach ($fromColumns as $fromColumn) {
                $fromColumn = get_object_vars($fromColumn);

                $fromColumn['id'] = $fromColumn['identifier'];

                $fromColumnsResult[] = $fromColumn;
            }

            foreach ($toColumns as $classToColumn) {
                $found = false;

                if (is_array($mappings)) {
                    foreach ($mappings as $mapping) {
                        if ($mapping->getToColumn() === $classToColumn->getIdentifier()) {
                            $found = true;

                            $mappingDefinition[] = [
                                'fromColumn' => $mapping->getFromColumn(),
                                'toColumn' => $mapping->getToColumn(),
                                'primaryIdentifier' => $mapping->getPrimaryIdentifier(),
                                'setter' => $mapping->getSetter(),
                                'setterConfig' => $mapping->getSetterConfig(),
                                'interpreter' => $mapping->getInterpreter(),
                                'interpreterConfig' => $mapping->getInterpreterConfig()
                            ];

                            break;
                        }
                    }
                }

                if (!$found) {
                    $mappingDefinition[] = [
                        'fromColumn' => null,
                        'toColumn' => $classToColumn->getIdentifier(),
                        'primaryIdentifier' => false,
                        'config' => $classToColumn->getConfig(),
                        'setter' => $classToColumn->getSetter(),
                        'setterConfig' => $classToColumn->getSetterConfig(),
                        'interpreter' => $classToColumn->getInterpreter(),
                        'interpreterConfig' => $classToColumn->getInterpreterConfig()
                    ];
                }
            }

            return $this->viewHandler->handle(['success' => true, 'mapping' => $mappingDefinition, 'fromColumns' => $fromColumnsResult, 'toColumns' => $toColumns]);
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function exportAction(Request $request)
    {
        $id = intval($request->get("id"));

        if ($id) {
            $definition = $this->repository->find($id);

            if ($definition instanceof DefinitionInterface) {

                $name = $definition->getName();
                unset($definition->id);
                unset($definition->creationDate);
                unset($definition->modificationDate);

                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->headers->set('Content-Disposition', 'attachment; filename="' . sprintf('import-definition-%s.json', $name) . '"');
                $response->headers->set('Pragma', "no-cache");
                $response->headers->set('Expires', "0");
                $response->headers->set('Content-Transfer-Encoding', "binary");

                $response->setContent(json_encode($definition));

                return $response;
            }
        }

        throw new NotFoundHttpException();
    }

    /**
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function importAction(Request $request)
    {
        $id = intval($request->get("id"));
        $definition = $this->repository->find($id);

        if ($id && $request->files->has('Filedata') && $definition instanceof DefinitionInterface) {
            $uploadedFile = $request->files->get('Filedata');

            if ($uploadedFile instanceof UploadedFile) {
                $jsonContent = file_get_contents($uploadedFile->getPathname());
                $data = $this->decodeJson($jsonContent, false);

                $form = $this->resourceFormFactory->create($this->metadata, $definition);
                $handledForm = $form->submit($data);

                if ($handledForm->isValid()) {
                    $definition = $form->getData();

                    $this->manager->persist($definition);
                    $this->manager->flush();

                    return $this->viewHandler->handle(['success' => true]);
                }
            }
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    /**
     * @param DataObject\ClassDefinition $class
     *
     * @return array
     */
    public function getClassDefinitionForFieldSelection(DataObject\ClassDefinition $class)
    {
        $fields = $class->getFieldDefinitions();

        $systemColumns = [
            "published", "key", "parent", "type"
        ];

        $result = array();

        $activatedLanguages = \Pimcore\Tool::getValidLanguages();

        foreach ($systemColumns as $sysColumn) {
            $toColumn = new ToColumn();

            $toColumn->setLabel($sysColumn);
            $toColumn->setFieldtype("input");
            $toColumn->setIdentifier($sysColumn);
            $toColumn->setType("systemColumn");

            $result[] = $toColumn;
        }

        foreach ($fields as $field) {
            if ($field instanceof DataObject\ClassDefinition\Data\Localizedfields) {
                foreach ($activatedLanguages as $language) {
                    $localizedFields = $field->getFieldDefinitions();

                    foreach ($localizedFields as $localizedField) {
                        $localizedField = $this->getFieldConfiguration($localizedField);

                        $localizedField->setType('localizedfield.' . $language);
                        $localizedField->setIdentifier($localizedField->getIdentifier() . "~" . $language);
                        $localizedField->setSetter('localizedfield');
                        $localizedField->setSetterConfig(array(
                            "language" => $language
                        ));
                        $result[] = $localizedField;
                    }
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Objectbricks) {
                $list = new DataObject\Objectbrick\Definition\Listing();
                $list = $list->load();

                foreach ($list as $brickDefinition) {
                    if ($brickDefinition instanceof DataObject\Objectbrick\Definition) {
                        $key = $brickDefinition->getKey();
                        $classDefs = $brickDefinition->getClassDefinitions();

                        foreach ($classDefs as $classDef) {
                            if ($classDef['classname'] === $class->getId() && $classDef['fieldname'] === $field->getName()) {
                                $fields = $brickDefinition->getFieldDefinitions();

                                foreach ($fields as $brickField) {
                                    $resultField = $this->getFieldConfiguration($brickField);

                                    $resultField->setType("objectbrick");
                                    $resultField->setIdentifier('objectbrick~' . $field->getName() . '~' . $key . '~' . $resultField->getIdentifier());
                                    $resultField->setSetter('objectbrick');
                                    $resultField->setSetterConfig(array(
                                        "class" => $key
                                    ));
                                    $result[] = $resultField;
                                }

                                break;
                            }
                        }
                    }
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Fieldcollections) {
                foreach ($field->getAllowedTypes() as $type) {
                    $definition = DataObject\Fieldcollection\Definition::getByKey($type);

                    $fieldDefinition = $definition->getFieldDefinitions();

                    foreach ($fieldDefinition as $fieldcollectionField) {
                        $resultField = $this->getFieldConfiguration($fieldcollectionField);

                        $resultField->setType("fieldcollection");
                        $resultField->setIdentifier("fieldcollection~" . $field->getName() . "~" . $type . "~" . $resultField->getIdentifier());
                        $resultField->setSetter('fieldcollection');
                        $resultField->setSetterConfig(array(
                            "class" => $type
                        ));

                        $result[] = $resultField;
                    }
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Classificationstore) {
                continue;
                $list = new DataObject\Classificationstore\GroupConfig\Listing();

                $allowedGroupIds = $field->getAllowedGroupIds();

                if ($allowedGroupIds) {
                    $list->setCondition('ID in (' . implode(',', $allowedGroupIds) . ')');
                }

                $list->load();

                $groupConfigList = $list->getList();

                foreach ($groupConfigList as $config) {
                    $key = $config->getId() . ($config->getName() ? $config->getName() : 'EMPTY');

                    foreach ($config->getRelations() as $relation) {
                        if ($relation instanceof DataObject\Classificationstore\KeyGroupRelation) {
                            $keyId = $relation->getKeyId();

                            $keyConfig = DataObject\Classificationstore\KeyConfig::getById($keyId);

                            $toColumn = new ToColumn();
                            $toColumn->setIdentifier('classificationstore~' . $field->getName() . '~' . $keyConfig->getId() . '~' . $config->getId());
                            $toColumn->setType("classificationstore");
                            $toColumn->setFieldtype($keyConfig->getType());
                            $toColumn->setSetter('classificationstore');
                            $toColumn->setSetterConfig(array(
                                "keyId" => $keyConfig->getId(),
                                "groupId" => $config->getId(),
                            ));
                            $toColumn->setLabel($keyConfig->getName());

                            $result[] = $toColumn;
                        }
                    }
                }
            } else {
                $result[] = $this->getFieldConfiguration($field);
            }
        }

        return $result;
    }

    /**
     * @param DataObject\ClassDefinition\Data $field
     * @return ToColumn
     */
    protected function getFieldConfiguration(DataObject\ClassDefinition\Data $field)
    {
        $toColumn = new ToColumn();

        $toColumn->setLabel($field->getName());
        $toColumn->setFieldtype($field->getFieldtype());
        $toColumn->setIdentifier($field->getName());

        return $toColumn;
    }

    /**
     * @return array
     */
    protected function getConfigProviders()
    {
        return $this->getParameter('import_definition.providers');
    }

    /**
     * @return array
     */
    protected function getConfigInterpreters()
    {
        return $this->getParameter('import_definition.interpreters');
    }

    /**
     * @return array
     */
    protected function getConfigCleaners()
    {
        return $this->getParameter('import_definition.cleaners');
    }

    /**
     * @return array
     */
    protected function getConfigSetters()
    {
        return $this->getParameter('import_definition.setters');
    }

    /**
     * @return array
     */
    protected function getConfigFilters()
    {
        return $this->getParameter('import_definition.filters');
    }

    /**
     * @return array
     */
    protected function getConfigRunners()
    {
        return $this->getParameter('import_definition.runners');
    }
}