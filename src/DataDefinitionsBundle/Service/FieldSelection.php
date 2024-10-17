<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Service;

use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping\ToColumn;
use Pimcore\Model\DataObject;
use Pimcore\Tool;

class FieldSelection
{
    public function getClassDefinition(DataObject\ClassDefinition $class): array
    {
        $fields = $class->getFieldDefinitions();

        $systemColumns = [
            'id',
            'published',
            'key',
            'parentId',
            'parent',
            'type',
        ];

        $result = [];

        $activatedLanguages = Tool::getValidLanguages();

        foreach ($systemColumns as $sysColumn) {
            $toColumn = new ToColumn();

            $toColumn->setLabel($sysColumn);
            $toColumn->setFieldtype('input');
            $toColumn->setIdentifier($sysColumn);
            $toColumn->setType('systemColumn');
            $toColumn->setGroup('systemColumn');

            $result[] = $toColumn;
        }

        foreach ($fields as $field) {
            if ($field instanceof DataObject\ClassDefinition\Data\Localizedfields) {
                foreach ($activatedLanguages as $language) {
                    $localizedFields = $field->getFieldDefinitions();

                    foreach ($localizedFields as $localizedField) {
                        $localizedField = $this->getFieldConfiguration($localizedField);

                        $localizedField->setGroup('localizedfield.' . strtolower($language));
                        $localizedField->setType('localizedfield.' . $language);
                        $localizedField->setIdentifier(sprintf('%s~%s', $localizedField->getIdentifier(), $language));
                        $localizedField->setSetter('localizedfield');
                        $localizedField->setConfig(['language' => $language]);
                        $localizedField->setSetterConfig(['language' => $language]);
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
                            if ($classDef['classname'] === $class->getName() &&
                                $classDef['fieldname'] === $field->getName()) {
                                $fields = $brickDefinition->getFieldDefinitions();

                                foreach ($fields as $brickField) {
                                    $resultField = $this->getFieldConfiguration($brickField);

                                    $resultField->setGroup('objectbrick.' . $key);
                                    $resultField->setType('objectbrick');
                                    $resultField->setIdentifier(
                                        sprintf(
                                            'objectbrick~%s~%s~%s',
                                            $field->getName(),
                                            $key,
                                            $resultField->getIdentifier(),
                                        ),
                                    );
                                    $resultField->setSetter('objectbrick');
                                    $resultField->setConfig(['class' => $key]);
                                    $result[] = $resultField;
                                }

                                break;
                            }
                        }
                    }
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Fieldcollections) {
                $result[] = $this->getFieldConfiguration($field);

                foreach ($field->getAllowedTypes() as $type) {
                    $definition = DataObject\Fieldcollection\Definition::getByKey($type);

                    $fieldDefinition = $definition->getFieldDefinitions();

                    foreach ($fieldDefinition as $fieldcollectionField) {
                        $resultField = $this->getFieldConfiguration($fieldcollectionField);

                        $resultField->setGroup('fieldcollection.' . $type);
                        $resultField->setType('fieldcollection');
                        $resultField->setIdentifier(
                            sprintf(
                                'fieldcollection~%s~%s~%s',
                                $field->getName(),
                                $type,
                                $resultField->getIdentifier(),
                            ),
                        );
                        $resultField->setSetter('fieldcollection');
                        $resultField->setConfig(['class' => $type]);

                        $result[] = $resultField;
                    }
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Classificationstore) {
                $list = new DataObject\Classificationstore\GroupConfig\Listing();

                $allowedGroupIds = $field->getAllowedGroupIds();

                if ($allowedGroupIds) {
                    $list->setCondition(
                        'ID in (' . implode(',', $allowedGroupIds) . ') AND storeId = ?',
                        [$field->getStoreId()],
                    );
                } else {
                    $list->setCondition('storeId = ?', [$field->getStoreId()]);
                }

                $list->load();

                $groupConfigList = $list->getList();

                /**
                 * @var DataObject\Classificationstore\GroupConfig $config
                 */
                foreach ($groupConfigList as $config) {
                    foreach ($config->getRelations() as $relation) {
                        if ($relation instanceof DataObject\Classificationstore\KeyGroupRelation) {
                            $keyId = $relation->getKeyId();

                            $keyConfig = DataObject\Classificationstore\KeyConfig::getById($keyId);

                            $toColumn = new ToColumn();
                            $toColumn->setGroup(
                                sprintf('classificationstore - %s (%s)', $config->getName(), $config->getId()),
                            );
                            $toColumn->setIdentifier(
                                sprintf(
                                    'classificationstore~%s~%s~%s',
                                    $field->getName(),
                                    $keyConfig->getId(),
                                    $config->getId(),
                                ),
                            );
                            $toColumn->setType('classificationstore');
                            $toColumn->setFieldtype($keyConfig->getType());
                            $toColumn->setSetter('classificationstore');
                            $toColumn->setConfig([
                                'field' => $field->getName(),
                                'keyId' => $keyConfig->getId(),
                                'groupId' => $config->getId(),
                            ]);
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

    protected function getFieldConfiguration(DataObject\ClassDefinition\Data $field): ToColumn
    {
        $toColumn = new ToColumn();

        $toColumn->setLabel($field->getName());
        $toColumn->setFieldtype($field->getFieldtype());
        $toColumn->setIdentifier($field->getName());
        $toColumn->setGroup('fields');

        return $toColumn;
    }
}
