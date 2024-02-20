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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Model;

use Exception;
use Pimcore\Model\AbstractModel;
use Instride\Bundle\DataDefinitionsBundle\Model\Log\Dao;

class Log extends AbstractModel
{
    protected $id;
    protected $definition;
    protected $o_id;

    public static function getById($id)
    {
        if (!is_numeric($id) || $id < 1) {
            return null;
        }

        try {
            $obj = new self;

            /**
             * @var Dao $dao
             */
            $dao = $obj->getDao();
            $dao->getById($id);

            return $obj;
        } catch (Exception $ex) {
            return null;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    public function setDefinition($definition)
    {
        $this->definition = $definition;
    }

    public function getO_Id()
    {
        return $this->o_id;
    }

    public function setO_Id($o_id)
    {
        $this->o_id = $o_id;
    }
}
