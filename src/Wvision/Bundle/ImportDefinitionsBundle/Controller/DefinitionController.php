<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Wvision\Bundle\ImportDefinitionsBundle\Model\DefinitionInterface;
use Wvision\Bundle\ImportDefinitionsBundle\Model\Mapping\FromColumn;
use Pimcore\Model\Object;
use Wvision\Bundle\ImportDefinitionsBundle\Model\Mapping\ToColumn;

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

            $toColumns = $this->getClassDefinitionForFieldSelection(Object\ClassDefinition::getByName($definition->getClass()));
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
                                'config' => $mapping->getConfig(),
                                'setterConfig' => $mapping->getSetterConfig(),
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
                        'setterConfig' => $classToColumn->getSetterConfig(),
                        'interpreterConfig' => $classToColumn->getInterpreterConfig()
                    ];
                }
            }

            return $this->viewHandler->handle(['success' => true, 'mapping' => $mappingDefinition, 'fromColumns' => $fromColumnsResult, 'toColumns' => $toColumns]);
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    /**
     * @param Object\ClassDefinition $class
     *
     * @return array
     */
    public function getClassDefinitionForFieldSelection(Object\ClassDefinition $class)
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
            if ($field instanceof Object\ClassDefinition\Data\Localizedfields) {
                foreach ($activatedLanguages as $language) {
                    $localizedFields = $field->getFieldDefinitions();

                    foreach ($localizedFields as $localizedField) {
                        $localizedField = $this->getFieldConfiguration($localizedField);

                        $localizedField->setType('localizedfield.' . $language);
                        $localizedField->setIdentifier($localizedField->getIdentifier() . "~" . $language);
                        $localizedField->setSetterConfig([
                            "setter" => "localizedfield"
                        ]);
                        $localizedField->setSetterConfig(array(
                            "language" => $language
                        ));
                        $result[] = $localizedField;
                    }
                }
            } elseif ($field instanceof Object\ClassDefinition\Data\Objectbricks) {
                $list = new Object\Objectbrick\Definition\Listing();
                $list = $list->load();

                foreach ($list as $brickDefinition) {
                    if ($brickDefinition instanceof Object\Objectbrick\Definition) {
                        $key = $brickDefinition->getKey();
                        $classDefs = $brickDefinition->getClassDefinitions();

                        foreach ($classDefs as $classDef) {
                            if ($classDef['classname'] === $class->getId() && $classDef['fieldname'] === $field->getName()) {
                                $fields = $brickDefinition->getFieldDefinitions();

                                foreach ($fields as $brickField) {
                                    $resultField = $this->getFieldConfiguration($brickField);

                                    $resultField->setType("objectbrick");
                                    $resultField->setIdentifier('objectbrick~' . $field->getName() . '~' . $key . '~' . $resultField->getIdentifier());
                                    $resultField->setConfig([
                                        "setter" => "objectbrick"
                                    ]);
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
            } elseif ($field instanceof Object\ClassDefinition\Data\Fieldcollections) {
                foreach ($field->getAllowedTypes() as $type) {
                    $definition = Object\Fieldcollection\Definition::getByKey($type);

                    $fieldDefinition = $definition->getFieldDefinitions();

                    foreach ($fieldDefinition as $fieldcollectionField) {
                        $resultField = $this->getFieldConfiguration($fieldcollectionField);

                        $resultField->setType("fieldcollection");
                        $resultField->setIdentifier("fieldcollection~" . $field->getName() . "~" . $type . "~" . $resultField->getIdentifier());
                        $resultField->setConfig([
                            "setter" => "fieldcollection"
                        ]);
                        $resultField->setSetterConfig(array(
                            "class" => $type
                        ));

                        $result[] = $resultField;
                    }
                }
            } elseif ($field instanceof Object\ClassDefinition\Data\Classificationstore) {
                $list = new Object\Classificationstore\GroupConfig\Listing();

                $allowedGroupIds = $field->getAllowedGroupIds();

                if ($allowedGroupIds) {
                    $list->setCondition('ID in (' . implode(',', $allowedGroupIds) . ')');
                }

                $list->load();

                $groupConfigList = $list->getList();

                foreach ($groupConfigList as $config) {
                    $key = $config->getId() . ($config->getName() ? $config->getName() : 'EMPTY');

                    foreach ($config->getRelations() as $relation) {
                        if ($relation instanceof Object\Classificationstore\KeyGroupRelation) {
                            $keyId = $relation->getKeyId();

                            $keyConfig = Object\Classificationstore\KeyConfig::getById($keyId);

                            $toColumn = new ToColumn();
                            $toColumn->setIdentifier('classificationstore~' . $field->getName() . '~' . $keyConfig->getId() . '~' . $config->getId());
                            $toColumn->setType("classificationstore");
                            $toColumn->setFieldtype($keyConfig->getType());
                            $toColumn->setConfig([

                                "setter" => "classificationstore"
                            ]);
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
     * @param Object\ClassDefinition\Data $field
     * @return ToColumn
     */
    protected function getFieldConfiguration(Object\ClassDefinition\Data $field)
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