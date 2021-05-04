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

namespace Wvision\Bundle\DataDefinitionsBundle\Model;

use Exception;
use Pimcore\Model\AbstractModel;

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
            $obj->getDao()->getById($id);

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
