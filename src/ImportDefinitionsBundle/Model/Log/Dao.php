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

namespace ImportDefinitionsBundle\Model\Log;

use Pimcore\Model\Dao\AbstractDao;

class Dao extends AbstractDao
{
    protected $tableName = 'import_definitions_log';

    /**
     * Get log by id
     *
     * @param null $id
     * @throws \Exception
     */
    public function getById($id = null)
    {
        if ($id !== null) {
            $this->model->setId($id);
        }

        $data = $this->db->fetchRow('SELECT * FROM ' . $this->tableName . ' WHERE id = ?', $this->model->getId());

        if (!$data['id']) {
            throw new \InvalidArgumentException(sprintf('Object with the ID %s does not exist', $this->model->getId()));
        }

        $this->assignVariablesToModel($data);
    }

    /**
     * Save log
     *
     * @throws \Exception
     */
    public function save()
    {
        $vars = get_object_vars($this->model);

        $buffer = [];

        $validColumns = $this->getValidTableColumns($this->tableName);

        if (\count($vars)) {
            foreach ($vars as $k => $v) {
                if (!\in_array($k, $validColumns, true)) {
                    continue;
                }

                $getter = sprintf('get%s', ucfirst($k));

                if (!\is_callable([$this->model, $getter])) {
                    continue;
                }

                $value = $this->model->$getter();

                if (\is_bool($value)) {
                    $value = (int) $value;
                }

                $buffer[$k] = $value;
            }
        }

        if ($this->model->getId() !== null) {
            $this->db->update($this->tableName, $buffer, ['id' => $this->model->getId()]);
            return;
        }

        $this->db->insert($this->tableName, $buffer);
        $this->model->setId($this->db->lastInsertId());
    }

    /**
     * Delete vote
     *
     * @throws \Exception
     */
    public function delete()
    {
        $this->db->delete($this->tableName, ['id' => $this->model->getId()]);
    }
}
