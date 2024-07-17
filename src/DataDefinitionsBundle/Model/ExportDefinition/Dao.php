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

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinition;

use Exception;
use InvalidArgumentException;
use Pimcore\Model;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportMapping;
use function count;
use function in_array;
use function is_array;

class Dao extends Model\Dao\PimcoreLocationAwareConfigDao
{
    private const CONFIG_KEY = 'export_definitions';

    /**
     * Configure Configuration File
     */
    public function configure(): void
    {
        $config = \Pimcore::getContainer()->getParameter('data_definitions.config_location');
        $definitions = \Pimcore::getContainer()->getParameter('data_definitions.export_definitions');

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
                $maps = array();

                foreach ($this->model->getMapping() as $map) {
                    if (is_array($map)) {
                        $mapObj = new ExportMapping();
                        $mapObj->setValues($map);

                        $maps[] = $mapObj;
                    }
                }

                $this->model->setMapping($maps);
            }
        }
    }

    /**
     * Get Definition by name.
     *
     * @param null $name
     * @throws Exception
     */
    public function getByName(string $id = null): void
    {
        if ($id != null) {
            $this->model->setName($id);
        }

        $data = $this->getDataByName($this->model->getName());

        if ($data && $id != null) {
            $data['id'] = $id;
        }

        if ($data) {
            $this->assignVariablesToModel($data);
            $this->model->setName($data['id']);
        } else {
            throw new Model\Exception\NotFoundException(sprintf(
                'Thumbnail with ID "%s" does not exist.',
                $this->model->getName()
            ));
        }
    }

    /**
     * Save Configuration
     *
     * @throws Exception
     */
    public function save()
    {
        $ts = time();
        if (!$this->model->getCreationDate()) {
            $this->model->setCreationDate($ts);
        }
        $this->model->setModificationDate($ts);

        $dataRaw = get_object_vars($this->model);
        $data = [];
        $allowedProperties = [
            'name',
            'provider',
            'class',
            'configuration',
            'creationDate',
            'modificationDate',
            'mapping',
            'runner',
            'stopOnException',
            'enableInheritance',
            'fetchUnpublished',
            'failureNotificationDocument',
            'successNotificationDocument',
            'fetcher',
            'fetcherConfig',
        ];

        foreach ($dataRaw as $key => $value) {
            if (in_array($key, $allowedProperties, true)) {
                if ($key === 'providerConfiguration') {
                    if ($value) {
                        $data[$key] = get_object_vars($value);
                    }
                } elseif ($key === 'mapping') {
                    if ($value) {
                        $data[$key] = array();

                        if (is_array($value)) {
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
        $this->saveData($this->model->getName(), $data);
    }

    protected function prepareDataStructureForYaml(string $id, mixed $data): mixed
    {
        return [
            'data_definitions' => [
                'export_definitions' => [
                    $id => $data,
                ],
            ],
        ];
    }

    /**
     * Deletes object from database
     * @throws Exception
     */
    public function delete()
    {
        $this->deleteData($this->model->getName());
    }
}
