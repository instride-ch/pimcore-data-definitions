<?php

use Pimcore\Controller\Action\Admin;
use Pimcore\Model\Object;

/**
 * Controller for Definitions
 *
 * Class AdvancedImportExport_Admin_DefinitionController
 */
class AdvancedImportExport_Admin_DefinitionController extends Admin
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

    public function getProvidersAction() {
        $this->_helper->json(array(
            'success' => true,
            'providers' => \AdvancedImportExport\Model\AbstractProvider::$availableProviders
        ));
    }

    public function listAction()
    {
        $list = new \AdvancedImportExport\Model\Definition\Listing();

        $data = array();
        if (is_array($list->getDefinitions())) {
            foreach ($list->getDefinitions() as $definition) {
                $data[] = $this->getTreeNodeConfig($definition);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(\AdvancedImportExport\Model\Definition $definition)
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
            $definition = new \AdvancedImportExport\Model\Definition();
            $definition->setName($name);
            $definition->setConfiguration([]);
            $definition->save();

            $this->_helper->json(array('success' => true, 'data' => $definition));
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $definition = \AdvancedImportExport\Model\Definition::getById($id);

        if ($definition instanceof \AdvancedImportExport\Model\Definition) {
            $this->_helper->json(array('success' => true, 'data' => $definition));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $definition = \AdvancedImportExport\Model\Definition::getById($id);

        if ($data && $definition instanceof \AdvancedImportExport\Model\Definition) {
            $data = \Zend_Json::decode($this->getParam('data'));
            
            $definition->setValues($data);

                $providerClass = 'AdvancedImportExport\\Model\\Provider\\' . ucfirst($definition->getProvider());
            
            if(\Pimcore\Tool::classExists($providerClass)) {
                $provider = new $providerClass();

                if($provider instanceof \AdvancedImportExport\Model\AbstractProvider) {
                    $provider->setValues($data['configuration']);

                    $definition->setProviderConfiguration($provider);
                }
                else {
                    $this->_helper->json(array('success' => false, 'message' => 'Provider Class found, but it needs to inherit from AdvancedImportExport\Model\AbstractProvider'));
                }
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
        $definition = \AdvancedImportExport\Model\Definition::getById($id);

        if ($definition instanceof \AdvancedImportExport\Model\Definition) {
            $definition->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }

    public function getColumnsAction() {
        $id = $this->getParam('id');

        $definition = \AdvancedImportExport\Model\Definition::getById($id);

        if ($definition instanceof \AdvancedImportExport\Model\Definition) {
            $headers = $definition->getProviderConfiguration()->getColumns();

            $this->_helper->json(array('success' => true, 'fromColumns' => $headers, 'toColumns' => $this->getClassDefinitionForFieldSelection(Object\ClassDefinition::getByName($definition->getClass()))));
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

        $result = array(
            'fields' => array(
                'nodeLabel' => 'fields',
                'nodeType' => 'object',
                'childs' => array(),
            ),
        );

        foreach ($fields as $field) {
            if ($field instanceof Object\ClassDefinition\Data\Localizedfields) {
                $localizedFields = $field->getFieldDefinitions();

                foreach ($localizedFields as $localizedField) {
                    $result[] = $this->getFieldConfiguration($localizedField);
                }
            } elseif ($field instanceof Object\ClassDefinition\Data\Objectbricks) {
                $list = new Object\Objectbrick\Definition\Listing();
                $list = $list->load();

                foreach ($list as $brickDefinition) {
                    if ($brickDefinition instanceof Object\Objectbrick\Definition) {
                        $key = $brickDefinition->getKey();
                        $classDefs = $brickDefinition->getClassDefinitions();

                        foreach ($classDefs as $classDef) {
                            if ($classDef['classname'] === $class->getId()) {
                                $fields = $brickDefinition->getFieldDefinitions();

                                $result[$key] = array();
                                $result[$key]['nodeLabel'] = $key;
                                $result[$key]['className'] = $key;
                                $result[$key]['nodeType'] = 'objectbricks';
                                $result[$key]['childs'] = array();

                                foreach ($fields as $field) {
                                    $result[$key]['childs'][] = $this->getFieldConfiguration($field);
                                }

                                break;
                            }
                        }
                    }
                }
            } elseif ($field instanceof Object\ClassDefinition\Data\Fieldcollections) {
                //TODO: implement FieldCollection
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

                    $result[$key] = $this->getClassificationStoreGroupConfiguration($config);
                }
            } else {
                $result['fields']['childs'][] = $this->getFieldConfiguration($field);
            }
        }

        return $result;
    }

    /**
     * @param Object\Classificationstore\GroupConfig $config
     * @return array
     */
    protected function getClassificationStoreGroupConfiguration(Object\Classificationstore\GroupConfig $config)
    {
        $result = array();
        $result['nodeLabel'] = $config->getName();
        $result['nodeType'] = 'classificationstore';
        $result['childs'] = array();

        foreach ($config->getRelations() as $relation) {
            if ($relation instanceof Object\Classificationstore\KeyGroupRelation) {
                $keyId = $relation->getKeyId();

                $keyConfig = Object\Classificationstore\KeyConfig::getById($keyId);

                $result['childs'][] = $this->getClassificationStoreFieldConfiguration($keyConfig, $config);
            }
        }

        return $result;
    }

    /**
     * @param Object\ClassDefinition\Data $field
     * @return array
     */
    protected function getFieldConfiguration(Object\ClassDefinition\Data $field)
    {
        return array(
            'name' => $field->getName(),
            'fieldtype' => $field->getFieldtype(),
            'title' => $field->getTitle(),
            'tooltip' => $field->getTooltip(),
        );
    }

    /**
     * @param Object\Classificationstore\KeyConfig $field
     * @param Object\Classificationstore\GroupConfig $groupConfig
     * @return array
     */
    protected function getClassificationStoreFieldConfiguration(Object\Classificationstore\KeyConfig $field, Object\Classificationstore\GroupConfig $groupConfig)
    {
        return array(
            'name' => $field->getName(),
            'fieldtype' => $field->getType(),
            'title' => $field->getName(),
            'tooltip' => $field->getDescription(),
            'keyConfigId' => $field->getId(),
            'groupConfigId' => $groupConfig->getId(),
        );
    }
}