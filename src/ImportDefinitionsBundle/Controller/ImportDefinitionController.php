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
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use ImportDefinitionsBundle\Model\ImportDefinitionInterface;
use ImportDefinitionsBundle\Model\ImportMapping\FromColumn;
use ImportDefinitionsBundle\Model\ImportMapping\ToColumn;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImportDefinitionController extends ResourceController
{
    /**
     * @return mixed|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getConfigAction()
    {
        $providers = $this->getConfigProviders();
        $loaders = $this->getConfigLoaders();
        $interpreters = $this->getConfigInterpreters();
        $cleaners = $this->getConfigCleaners();
        $setters = $this->getConfigSetters();
        $filters = $this->getConfigFilters();
        $runners = $this->getConfigRunners();

        return $this->viewHandler->handle([
            'providers' => array_keys($providers),
            'loaders' => array_keys($loaders),
            'interpreter' => array_keys($interpreters),
            'cleaner' => array_keys($cleaners),
            'setter' => array_keys($setters),
            'filters' => array_keys($filters),
            'runner' => array_keys($runners)
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function testDataAction(Request $request)
    {
        $id = $request->get('id');
        $definition = $this->repository->find($id);

        if ($definition instanceof ImportDefinitionInterface) {
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
     * @throws \Exception
     */
    public function getColumnsAction(Request $request)
    {
        $id = $request->get('id');
        $definition = $this->repository->find($id);

        if ($definition instanceof ImportDefinitionInterface && $definition->getClass()) {
            $customFromColumn = new FromColumn();
            $customFromColumn->setIdentifier('custom');
            $customFromColumn->setLabel('Custom');

            try {
                $fromColumns = $this->get('import_definition.registry.provider')->get($definition->getProvider())->getColumns($definition->getConfiguration());
                $fromColumns[] = $customFromColumn;
            } catch (\Exception $e) {
                $fromColumns = [];
            }

            $classDefinition = DataObject\ClassDefinition::getByName($definition->getClass());
            $toColumns = $this->getClassDefinitionForFieldSelection($classDefinition);
            $mappings = $definition->getMapping();
            $mappingDefinition = [];
            $fromColumnsResult = [];
            $bricks = [];
            $collections = [];

            foreach ($classDefinition->getFieldDefinitions() as $field) {
                if ($field instanceof DataObject\ClassDefinition\Data\Objectbricks) {
                    $bricks[$field->getName()] = $field->getAllowedTypes();
                } elseif ($field instanceof DataObject\ClassDefinition\Data\Fieldcollections) {
                    $collections[$field->getName()] = $field->getAllowedTypes();
                }
            }

            foreach ($fromColumns as $fromColumn) {
                $fromColumn = get_object_vars($fromColumn);

                $fromColumn['id'] = $fromColumn['identifier'];

                $fromColumnsResult[] = $fromColumn;
            }

            foreach ($toColumns as $classToColumn) {
                $found = false;

                if (\is_array($mappings)) {
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
                        'identifier' => null,
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

            return $this->viewHandler->handle([
                'success' => true,
                'mapping' => $mappingDefinition,
                'fromColumns' => $fromColumnsResult,
                'toColumns' => $toColumns,
                'bricks' => $bricks,
                'fieldcollections' => $collections
            ]);
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function exportAction(Request $request): Response
    {
        $id = (int) $request->get('id');

        if ($id) {
            $definition = $this->repository->find($id);

            if ($definition instanceof ImportDefinitionInterface) {

                $name = $definition->getName();
                unset($definition->id, $definition->creationDate, $definition->modificationDate);

                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->headers->set('Content-Disposition', sprintf('attachment; filename="import-definition-%s.json"', $name));
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
                $response->headers->set('Content-Transfer-Encoding', 'binary');

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
        $id = (int) $request->get('id');
        $definition = $this->repository->find($id);

        if ($id && $definition instanceof ImportDefinitionInterface && $request->files->has('Filedata')) {
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
     * @return array
     * @throws \Exception
     */
    public function getClassDefinitionForFieldSelection(DataObject\ClassDefinition $class): array
    {
        $fields = $class->getFieldDefinitions();
        $csLoadedGroupIds = [];

        $systemColumns = [
            'o_published', 'o_key', 'o_parentId', 'o_parent', 'o_type'
        ];

        $result = [];

        $activatedLanguages = \Pimcore\Tool::getValidLanguages();

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
                            if ($classDef['classname'] === $class->getName() && $classDef['fieldname'] === $field->getName()) {
                                $fields = $brickDefinition->getFieldDefinitions();

                                foreach ($fields as $brickField) {
                                    $resultField = $this->getFieldConfiguration($brickField);

                                    $resultField->setGroup('objectbrick.' . $key);
                                    $resultField->setType('objectbrick');
                                    $resultField->setIdentifier(sprintf('objectbrick~%s~%s~%s', $field->getName(), $key, $resultField->getIdentifier()));
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
                foreach ($field->getAllowedTypes() as $type) {
                    $definition = DataObject\Fieldcollection\Definition::getByKey($type);

                    $fieldDefinition = $definition->getFieldDefinitions();

                    foreach ($fieldDefinition as $fieldcollectionField) {
                        $resultField = $this->getFieldConfiguration($fieldcollectionField);

                        $resultField->setGroup('fieldcollection.' . $type);
                        $resultField->setType('fieldcollection');
                        $resultField->setIdentifier(sprintf('fieldcollection~%s~%s~%s', $field->getName(), $type, $resultField->getIdentifier()));
                        $resultField->setSetter('fieldcollection');
                        $resultField->setConfig(['class' => $type]);

                        $result[] = $resultField;
                    }
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Classificationstore) {
                $list = new DataObject\Classificationstore\GroupConfig\Listing();

                $allowedGroupIds = $field->getAllowedGroupIds();

                if ($allowedGroupIds) {
                    $list->setCondition('ID in (' . implode(',', $allowedGroupIds) . ') AND storeId = ?', [$field->getStoreId()]);
                }
                else {
                    $list->setCondition('storeId = ?', [$field->getStoreId()]);
                }

                $list->load();

                $groupConfigList = $list->getList();

                /**
                 * @var DataObject\Classificationstore\GroupConfig $config
                 */
                foreach ($groupConfigList as $config) {
                    if (in_array($config->getId(), $csLoadedGroupIds)) {
                        continue;
                    }

                    foreach ($config->getRelations() as $relation) {
                        if ($relation instanceof DataObject\Classificationstore\KeyGroupRelation) {
                            $keyId = $relation->getKeyId();

                            $keyConfig = DataObject\Classificationstore\KeyConfig::getById($keyId);

                            $toColumn = new ToColumn();
                            $toColumn->setGroup(sprintf('classificationstore - %s (%s)', $config->getName(), $config->getId()));
                            $toColumn->setIdentifier(sprintf('classificationstore~%s~%s~%s', $field->getName(), $keyConfig->getId(), $config->getId()));
                            $toColumn->setType('classificationstore');
                            $toColumn->setFieldtype($keyConfig->getType());
                            $toColumn->setSetter('classificationstore');
                            $toColumn->setConfig([
                                'keyId' => $keyConfig->getId(),
                                'groupId' => $config->getId(),
                            ]);
                            $toColumn->setLabel($keyConfig->getName());

                            $result[] = $toColumn;
                        }
                    }

                    $csLoadedGroupIds[] = $config->getId();
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
    protected function getFieldConfiguration(DataObject\ClassDefinition\Data $field): ToColumn
    {
        $toColumn = new ToColumn();

        $toColumn->setLabel($field->getName());
        $toColumn->setFieldtype($field->getFieldtype());
        $toColumn->setIdentifier($field->getName());
        $toColumn->setGroup('fields');

        return $toColumn;
    }

    /**
     * @return array
     */
    protected function getConfigProviders(): array
    {
        return $this->getParameter('import_definition.providers');
    }

    /**
     * @return array
     */
    protected function getConfigLoaders(): array
    {
        return $this->getParameter('import_definition.loaders');
    }

    /**
     * @return array
     */
    protected function getConfigInterpreters(): array
    {
        return $this->getParameter('import_definition.interpreters');
    }

    /**
     * @return array
     */
    protected function getConfigCleaners(): array
    {
        return $this->getParameter('import_definition.cleaners');
    }

    /**
     * @return array
     */
    protected function getConfigSetters(): array
    {
        return $this->getParameter('import_definition.setters');
    }

    /**
     * @return array
     */
    protected function getConfigFilters(): array
    {
        return $this->getParameter('import_definition.filters');
    }

    /**
     * @return array
     */
    protected function getConfigRunners(): array
    {
        return $this->getParameter('import_definition.runners');
    }
}