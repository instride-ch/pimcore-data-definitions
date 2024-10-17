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

namespace Instride\Bundle\DataDefinitionsBundle\Model\Log;

use function count;
use Exception;
use function in_array;
use InvalidArgumentException;
use function is_bool;
use function is_callable;
use Pimcore\Model\Dao\AbstractDao;

class Dao extends AbstractDao
{
    protected string $tableName = 'data_definitions_import_log';

    /**
     * Get log by id
     *
     * @param null $id
     *
     * @throws Exception
     */
    public function getById($id = null)
    {
        if ($id !== null) {
            $this->model->setId($id);
        }

        $data = $this->db->fetchAssociative('SELECT * FROM ' . $this->tableName . ' WHERE id = ?', [$this->model->getId()]);

        if (!$data['id']) {
            throw new InvalidArgumentException(sprintf('Object with the ID %s does not exist', $this->model->getId()));
        }

        $this->assignVariablesToModel($data);
    }

    /**
     * Save log
     *
     * @throws Exception
     */
    public function save()
    {
        $vars = $this->model->getObjectVars();

        $buffer = [];

        $validColumns = $this->getValidTableColumns($this->tableName);

        if (count($vars)) {
            foreach ($vars as $k => $v) {
                if (!in_array($k, $validColumns, true)) {
                    continue;
                }

                $getter = sprintf('get%s', ucfirst($k));

                if (!is_callable([$this->model, $getter])) {
                    continue;
                }

                $value = $this->model->$getter();

                if (is_bool($value)) {
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
        $this->model->setId((int) $this->db->lastInsertId());
    }

    /**
     * Delete vote
     *
     * @throws Exception
     */
    public function delete()
    {
        $this->db->delete($this->tableName, ['id' => $this->model->getId()]);
    }
}
