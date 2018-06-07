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

namespace ImportDefinitionsBundle\Model\Definition;

use Pimcore\Model;

use ImportDefinitionsBundle\Model\Mapping;

class Dao extends Model\Dao\PhpArrayTable
{
    /**
     * Configure Configuration File
     */
    public function configure()
    {
        parent::configure();
        $this->setFile('importdefinitions');
    }

    /**
     * Get Configuration By Id
     *
     * @param null $id
     * @throws \Exception
     */
    public function getById($id = null)
    {
        if ($id !== null) {
            $this->model->setId($id);
        }

        $data = $this->db->getById($this->model->getId());

        if (isset($data['id'])) {
            $this->assignVariablesToModel($data);
        } else {
            throw new \InvalidArgumentException(sprintf('Definition with id: %s does not exist', $this->model->getId()));
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws \Exception
     */
    protected function assignVariablesToModel($data)
    {
        parent::assignVariablesToModel($data);

        foreach ($data as $key => $value) {
            if ($key === 'mapping') {
                $maps = array();

                foreach ($this->model->getMapping() as $index=>$map) {
                    if (\is_array($map)) {
                        $mapObj = new Mapping();
                        $mapObj->setValues($map);

                        $maps[$index] = $mapObj;
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
     * @throws \Exception
     */
    public function getByName($name = null)
    {
        if ($name !== null) {
            $this->model->setName($name);
        }

        $name = $this->model->getName();

        $data = $this->db->fetchAll(function ($row) use ($name) {
            return $row['name'] === $name;
        });

        if ($data[0]['id'] && \count($data)) {
            $this->assignVariablesToModel($data[0]);
        } else {
            throw new \InvalidArgumentException(sprintf('Definition with name: %s does not exist', $this->model->getName()));
        }
    }

    /**
     * Save Configuration
     *
     * @throws \Exception
     */
    public function save()
    {
        $ts = time();
        if (!$this->model->getCreationDate()) {
            $this->model->setCreationDate($ts);
        }
        $this->model->setModificationDate($ts);

        try {
            $dataRaw = get_object_vars($this->model);
            $data = [];
            $allowedProperties = ['id', 'name', 'provider', 'class', 'configuration', 'creationDate',
                'modificationDate', 'mapping', 'objectPath', 'cleaner', 'key', 'renameExistingObjects',
                'relocateExistingObjects', 'filter', 'runner', 'createVersion', 'stopOnException', 'omitMandatoryCheck',
                'failureNotificationDocument', 'successNotificationDocument', 'skipExistingObjects', 'skipNewObjects'];

            foreach ($dataRaw as $key => $value) {
                if (\in_array($key, $allowedProperties, true)) {
                    if ($key === 'providerConfiguration') {
                        if ($value) {
                            $data[$key] = get_object_vars($value);
                        }
                    } elseif ($key === 'mapping') {
                        if ($value) {
                            $data[$key] = array();

                            if (\is_array($value)) {
                                foreach ($value as $index => $map) {
                                    $data[$key][$index] = get_object_vars($map);
                                }
                            }
                        }
                    } else {
                        $data[$key] = $value;
                    }
                }
            }
            $this->db->insertOrUpdate($data, $this->model->getId());
        } catch (\Exception $e) {
            throw $e;
        }

        if (!$this->model->getId()) {
            $this->model->setId($this->db->getLastInsertId());
        }
    }

    /**
     * Deletes object from database
     * @throws \Exception
     */
    public function delete()
    {
        $this->db->delete($this->model->getId());
    }
}
