<?php

use Pimcore\Controller\Action\Admin;
use Pimcore\Model\Object;

/**
 * Controller for Definitions
 *
 * Class ImportDefinitions_Admin_DefinitionController
 */
class ImportDefinitions_Admin_DefinitionController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        //TODO: Permissions?
        /*$notRestrictedActions = array('list');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_carriers');
        }*/
    }

    public function getConfigAction() {
        $this->_helper->json(array(
            'success' => true,
            'providers' => \ImportDefinitions\Model\AbstractProvider::$availableProviders,
            'interpreter' => \ImportDefinitions\Model\Interpreter\AbstractInterpreter::$availableInterpreter,
            'cleaner' => \ImportDefinitions\Model\Cleaner\AbstractCleaner::$availableCleaner,
            'setter' => \ImportDefinitions\Model\Setter\AbstractSetter::$availableSetter
        ));
    }

    public function listAction()
    {
        $list = new \ImportDefinitions\Model\Definition\Listing();

        $data = array();
        if (is_array($list->getDefinitions())) {
            foreach ($list->getDefinitions() as $definition) {
                $data[] = $this->getTreeNodeConfig($definition);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(\ImportDefinitions\Model\Definition $definition)
    {
        $tmp = array(
            'id' => $definition->getId(),
            'text' => $definition->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$definition->getId(),
            ),
            'name' => $definition->getName(),
        );

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $definition = new \ImportDefinitions\Model\Definition();
            $definition->setName($name);
            $definition->save();

            $this->_helper->json(array('success' => true, 'data' => $definition));
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $definition = \ImportDefinitions\Model\Definition::getById($id);

        if ($definition instanceof \ImportDefinitions\Model\Definition) {
            $this->_helper->json(array('success' => true, 'data' => $definition));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function testDataAction() {
        $id = $this->getParam("id");
        $definition = \ImportDefinitions\Model\Definition::getById($id);

        if ($definition instanceof \ImportDefinitions\Model\Definition) {
            try {
                if($definition->getProviderConfiguration()->testData()) {
                    $this->_helper->json(array('success' => true));
                }
            }
            catch(\Exception $ex) {
                $this->_helper->json(array('success' => false, 'message' => $ex->getMessage()));
            }

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $definition = \ImportDefinitions\Model\Definition::getById($id);

        if ($data && $definition instanceof \ImportDefinitions\Model\Definition) {
            $data = \Zend_Json::decode($this->getParam('data'));
            
            $definition->setValues($data);
            $providerClass = 'ImportDefinitions\\Model\\Provider\\' . ucfirst($definition->getProvider());
            
            if(\Pimcore\Tool::classExists($providerClass)) {
                $provider = new $providerClass();

                if($provider instanceof \ImportDefinitions\Model\AbstractProvider) {
                    $provider->setValues($data['configuration']);

                    $definition->setProviderConfiguration($provider);
                }
                else {
                    $this->_helper->json(array('success' => false, 'message' => 'Provider Class found, but it needs to inherit from ImportDefinitions\Model\AbstractProvider'));
                }

                $maps = [];

                foreach($data['mapping'] as $map) {
                    $mapping = new \ImportDefinitions\Model\Mapping();
                    $mapping->setValues($map);

                    $maps[] = $mapping;
                }

                $definition->setMapping($maps);
            }
            else {
                $this->_helper->json(array('success' => false, 'message' => 'Provider Class not found'));
            }
            
            $definition->save();

            $this->_helper->json(array('success' => true, 'data' => $definition));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $definition = \ImportDefinitions\Model\Definition::getById($id);

        if ($definition instanceof \ImportDefinitions\Model\Definition) {
            $definition->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }

    public function getColumnsAction() {
        $id = $this->getParam('id');

        $definition = \ImportDefinitions\Model\Definition::getById($id);

        if ($definition instanceof \ImportDefinitions\Model\Definition) {
            $customFromColumn = new \ImportDefinitions\Model\Mapping\FromColumn();
            $customFromColumn->setIdentifier('custom');
            $customFromColumn->setLabel('Custom');

            try {
                $fromColumns = $definition->getProviderConfiguration()->getColumns();
                $fromColumns[] = $customFromColumn;
            }
            catch(\Exception $e) {
                $fromColumns = [];
            }

            $toColumns = $this->getClassDefinitionForFieldSelection(Object\ClassDefinition::getByName($definition->getClass()));
            $mappings = $definition->getMapping();
            $mappingDefinition = [];
            $fromColumnsResult = [];

            foreach($fromColumns as $fromColumn) {
                $fromColumn = get_object_vars($fromColumn);

                $fromColumn['id'] = $fromColumn['identifier'];

                $fromColumnsResult[] = $fromColumn;
            }

            foreach($toColumns as $classToColumn) {
                $found = false;

                if(is_array($mappings)) {
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
            
            $this->_helper->json(array('success' => true, 'mapping' => $mappingDefinition, 'fromColumns' => $fromColumnsResult, 'toColumns' => $toColumns));
        }

        $this->_helper->json(array('success' => false));
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
            "published"
        ];

        $result = array(

        );

        $activatedLanguages = \Pimcore\Tool::getValidLanguages();

        foreach($systemColumns as $sysColumn) {
            $toColumn = new \ImportDefinitions\Model\Mapping\ToColumn();

            $toColumn->setLabel($sysColumn);
            $toColumn->setFieldtype("input");
            $toColumn->setIdentifier($sysColumn);
            $toColumn->setType("systemColumn");

            $result[] = $toColumn;
        }

        foreach ($fields as $field) {
            if ($field instanceof Object\ClassDefinition\Data\Localizedfields) {
                foreach($activatedLanguages as $language) {

                    $localizedFields = $field->getFieldDefinitions();

                    foreach ($localizedFields as $localizedField) {
                        $localizedField = $this->getFieldConfiguration($localizedField);

                        $localizedField->setType('localizedfield.' . $language);
                        $localizedField->setIdentifier($localizedField->getIdentifier() . "~" . $language);
                        $localizedField->setConfig([
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
                //TODO: implement FieldCollection

                foreach($field->getAllowedTypes() as $type) {
                    $definition = Object\Fieldcollection\Definition::getByKey($type);

                    $fieldDefinition = $definition->getFieldDefinitions();

                    foreach($fieldDefinition as $fieldcollectionField) {
                        $resultField = $this->getFieldConfiguration($fieldcollectionField);

                        $resultField->setType("fieldcollection");
                        $resultField->setIdentifier("fieldcollection~" .  $field->getName() . "~" . $type . "~" . $resultField->getIdentifier());
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
                    $list->setCondition('ID in ('.implode(',', $allowedGroupIds).')');
                }

                $list->load();

                $groupConfigList = $list->getList();

                foreach ($groupConfigList as $config) {
                    $key = $config->getId().($config->getName() ? $config->getName() : 'EMPTY');

                    foreach ($config->getRelations() as $relation) {
                        if ($relation instanceof Object\Classificationstore\KeyGroupRelation) {
                            $keyId = $relation->getKeyId();

                            $keyConfig = Object\Classificationstore\KeyConfig::getById($keyId);

                            $toColumn = new \ImportDefinitions\Model\Mapping\ToColumn();
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
     * @return \ImportDefinitions\Model\Mapping\ToColumn
     */
    protected function getFieldConfiguration(Object\ClassDefinition\Data $field)
    {
        $toColumn = new \ImportDefinitions\Model\Mapping\ToColumn();

        $toColumn->setLabel($field->getName());
        $toColumn->setFieldtype($field->getFieldtype());
        $toColumn->setIdentifier($field->getName());

        return $toColumn;
    }
}