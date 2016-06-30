<?php

namespace ImportDefinitions\Model;

use Pimcore\Model\AbstractModel;

/**
 * Class Log
 * @package ImportDefinitions\Model
 */
class Log extends AbstractModel {

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $definition;

    /**
     * @var int
     */
    public $o_id;

    /**
     * get Log by id
     *
     * @param $id
     * @return null|Log
     */
    public static function getById($id) {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        }
        catch (\Exception $ex) {
            \Logger::warn("Log with id $id not found");
        }

        return null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param int $definition
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;
    }

    /**
     * @return int
     */
    public function getO_Id()
    {
        return $this->o_id;
    }

    /**
     * @param int $o_id
     */
    public function setO_Id($o_id)
    {
        $this->o_id = $o_id;
    }
}