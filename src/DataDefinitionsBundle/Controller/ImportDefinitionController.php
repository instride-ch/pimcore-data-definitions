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

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Exception;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping\FromColumn;
use Instride\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;
use Instride\Bundle\DataDefinitionsBundle\Service\FieldSelection;
use function is_array;
use Pimcore\Model\DataObject;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\SubscribedService;

/**
 * @property DefinitionRepository $repository
 */
class ImportDefinitionController extends AbstractDefinitionController
{
    public function getConfigAction(): JsonResponse
    {
        $providers = $this->getConfigProviders();
        $loaders = $this->getConfigLoaders();
        $interpreters = $this->getConfigInterpreters();
        $cleaners = $this->getConfigCleaners();
        $setters = $this->getConfigSetters();
        $filters = $this->getConfigFilters();
        $runners = $this->getConfigRunners();
        $persisters = $this->getConfigPersisters();
        $importRuleConditions = $this->getImportRuleConditions();
        $importRuleActions = $this->getImportRuleActions();

        return $this->viewHandler->handle([
            'providers' => array_keys($providers),
            'loaders' => array_keys($loaders),
            'interpreter' => array_keys($interpreters),
            'cleaner' => array_keys($cleaners),
            'setter' => array_keys($setters),
            'filters' => array_keys($filters),
            'runner' => array_keys($runners),
            'persister' => array_keys($persisters),
            'import_rules' => [
                'conditions' => array_keys($importRuleConditions),
                'actions' => array_keys($importRuleActions),
            ],
        ]);
    }

    public function testDataAction(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $definition = $this->repository->find($id);

        if ($definition instanceof ImportDefinitionInterface) {
            try {
                if ($this->container->get('data_definitions.registry.provider')->get(
                    $definition->getProvider(),
                )->testData(
                    $definition->getConfiguration(),
                )) {
                    return $this->viewHandler->handle(['success' => true]);
                }
            } catch (Exception $ex) {
                return $this->viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    public function getColumnsAction(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $definition = $this->repository->find($id);

        if ($definition instanceof ImportDefinitionInterface && $definition->getClass()) {
            $customFromColumn = new FromColumn();
            $customFromColumn->setIdentifier('custom');
            $customFromColumn->setLabel('Custom');

            try {
                $fromColumns = $this->container->get('data_definitions.registry.provider')->get(
                    $definition->getProvider(),
                )->getColumns($definition->getConfiguration());
                $fromColumns[] = $customFromColumn;
            } catch (Exception $e) {
                $fromColumns = [];
            }

            $classDefinition = DataObject\ClassDefinition::getByName($definition->getClass());

            if (!$classDefinition) {
                throw new NotFoundHttpException();
            }

            $toColumns = $this->container->get(FieldSelection::class)->getClassDefinition($classDefinition);
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

                if (is_array($mappings)) {
                    /**
                     * @var ImportMapping $mapping
                     */
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
                                'interpreterConfig' => $mapping->getInterpreterConfig(),
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
                        'interpreterConfig' => $classToColumn->getInterpreterConfig(),
                    ];
                }
            }

            return $this->viewHandler->handle([
                'success' => true,
                'mapping' => $mappingDefinition,
                'fromColumns' => $fromColumnsResult,
                'toColumns' => $toColumns,
                'bricks' => $bricks,
                'fieldcollections' => $collections,
            ]);
        }

        return $this->viewHandler->handle(['success' => false]);
    }

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
                $response->headers->set(
                    'Content-Disposition',
                    sprintf('attachment; filename="import-definition-%s.json"', $name),
                );
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
                $response->headers->set('Content-Transfer-Encoding', 'binary');

                $response->setContent(json_encode($definition));

                return $response;
            }
        }

        throw new NotFoundHttpException();
    }

    public function importAction(Request $request): JsonResponse
    {
        $id = (int) $request->get('id');
        $definition = $this->repository->find($id);

        if ($id && $definition instanceof ImportDefinitionInterface && $request->files->has('Filedata')) {
            $uploadedFile = $request->files->get('Filedata');

            if ($uploadedFile instanceof UploadedFile) {
                $jsonContent = file_get_contents($uploadedFile->getPathname());
                $data = $this->decodeJson($jsonContent, false, [], false);

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

    public function duplicateAction(Request $request): JsonResponse
    {
        $id = (int) $request->get('id');
        $definition = $this->repository->find($id);
        $name = (string) $request->get('name');

        if ($definition instanceof ImportDefinitionInterface && $name) {
            $newDefinition = clone $definition;
            $newDefinition->setId(null);
            $newDefinition->setName($name);

            $this->manager->persist($newDefinition);
            $this->manager->flush();

            return $this->viewHandler->handle(['success' => true, 'data' => $newDefinition]);
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
                FieldSelection::class,
                new SubscribedService('data_definitions.registry.provider', ServiceRegistryInterface::class, attributes: new Autowire(service: 'data_definitions.registry.provider')),
            ];
    }

    protected function getConfigProviders(): array
    {
        return $this->getParameter('data_definitions.import_providers');
    }

    protected function getConfigLoaders(): array
    {
        return $this->getParameter('data_definitions.loaders');
    }

    protected function getConfigInterpreters(): array
    {
        return $this->getParameter('data_definitions.interpreters');
    }

    protected function getConfigCleaners(): array
    {
        return $this->getParameter('data_definitions.cleaners');
    }

    protected function getConfigSetters(): array
    {
        return $this->getParameter('data_definitions.setters');
    }

    protected function getConfigFilters(): array
    {
        return $this->getParameter('data_definitions.filters');
    }

    protected function getConfigRunners(): array
    {
        return $this->getParameter('data_definitions.runners');
    }

    protected function getConfigPersisters(): array
    {
        return $this->getParameter('data_definitions.persisters');
    }

    protected function getImportRuleConditions(): array
    {
        return $this->getParameter('data_definitions.import_rule.conditions');
    }

    protected function getImportRuleActions(): array
    {
        return $this->getParameter('data_definitions.import_rule.actions');
    }
}
