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

namespace Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition;

use Exception;
use Instride\Bundle\DataDefinitionsBundle\Model\IdGenerator;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportMapping;
use Pimcore\Model;

/**
 * @var ImportDefinition $model
 */
class Dao extends Model\Dao\PimcoreLocationAwareConfigDao
{
    use IdGenerator;

    private const CONFIG_KEY = 'import_definitions';

    /**
     * Configure Configuration File
     */
    public function configure(): void
    {
        $config = \Pimcore::getContainer()->getParameter('data_definitions.config_location');
        $definitions = \Pimcore::getContainer()->getParameter('data_definitions.import_definitions');

        $storageConfig = $config[self::CONFIG_KEY];

        parent::configure([
            'containerConfig' => $definitions,
            'settingsStoreScope' => 'data_definitions',
            'storageConfig' => $storageConfig,
        ]);
    }

    protected function assignVariablesToModel($data): void
    {
        parent::assignVariablesToModel($data);

        foreach ($data as $key => $value) {
            if ($key === 'mapping') {
                $maps = [];

                foreach ($this->model->getMapping() as $map) {
                    if (\is_array($map)) {
                        $mapObj = new ImportMapping();
                        $mapObj->setValues($map);

                        $maps[] = $mapObj;
                    }
                }

                $this->model->setMapping($maps);
            }
        }
    }

    /**
     * @throws Model\Exception\NotFoundException
     */
    public function getById(string $id)
    {
        $data = $this->getDataByName($id);

        if ($data) {
            $data['id'] = $id;
        }

        if ($data) {
            $this->assignVariablesToModel($data);
        } else {
            throw new Model\Exception\NotFoundException(sprintf(
                'Import Definition with ID "%s" does not exist.',
                $id,
            ));
        }
    }

    public function getByName(string $name): void
    {
        foreach ($this->loadIdList() as $id) {
            $definition = ImportDefinition::getById((int) $id);

            if ($definition->getName() === $name) {
                $this->getById((string) $id);

                return;
            }
        }

        throw new Model\Exception\NotFoundException(sprintf(
            'Import Definition with Name "%s" does not exist.',
            $name,
        ));
    }

    /**
     * @throws Exception
     */
    public function save()
    {
        $ts = time();

        if (!$this->model->getId()) {
            $this->model->setId($this->getSuggestedId(new Listing()));
        }

        if (!$this->model->getCreationDate()) {
            $this->model->setCreationDate($ts);
        }
        $this->model->setModificationDate($ts);

        $dataRaw = get_object_vars($this->model);
        $data = [];
        $allowedProperties = [
            'id',
            'name',
            'provider',
            'class',
            'configuration',
            'creationDate',
            'modificationDate',
            'mapping',
            'objectPath',
            'cleaner',
            'key',
            'renameExistingObjects',
            'relocateExistingObjects',
            'filter',
            'runner',
            'createVersion',
            'stopOnException',
            'omitMandatoryCheck',
            'failureNotificationDocument',
            'successNotificationDocument',
            'skipExistingObjects',
            'skipNewObjects',
            'forceLoadObject',
            'loader',
            'fetcher',
            'persister',
        ];

        foreach ($dataRaw as $key => $value) {
            if (in_array($key, $allowedProperties, true)) {
                if ($key === 'providerConfiguration') {
                    if ($value) {
                        $data[$key] = get_object_vars($value);
                    }
                } elseif ($key === 'mapping') {
                    if ($value) {
                        $data[$key] = [];

                        if (\is_array($value)) {
                            foreach ($value as $map) {
                                $data[$key][] = get_object_vars($map);
                            }
                        }
                    }
                } else {
                    $data[$key] = $value;
                }
            }
        }

        $this->saveData((string) $this->model->getId(), $data);
    }

    protected function prepareDataStructureForYaml(string $id, mixed $data): mixed
    {
        return [
            'data_definitions' => [
                'import_definitions' => [
                    $id => $data,
                ],
            ],
        ];
    }

    /**
     * Deletes object from database
     *
     * @throws Exception
     */
    public function delete()
    {
        $this->deleteData((string) $this->model->getId());
    }
}
