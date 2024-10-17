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

namespace Instride\Bundle\DataDefinitionsBundle\Model;

use Exception;
use Instride\Bundle\DataDefinitionsBundle\Model\Log\Dao;
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
            $obj = new self();

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
