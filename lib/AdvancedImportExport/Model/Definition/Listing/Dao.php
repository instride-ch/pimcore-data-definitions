<?php

namespace AdvancedImportExport\Model\Definition\Listing;

use Pimcore;
use AdvancedImportExport\Model;

class Dao extends Pimcore\Model\Dao\PhpArrayTable
{
    /**
     * configure.
     */
    public function configure()
    {
        parent::configure();
        $this->setFile('advancedimportexport_definitions');
    }

    /**
     * Loads a list of Definitions for the specicifies parameters, returns an array of Definitions elements.
     *
     * @return array
     */
    public function load()
    {
        $routesData = $this->db->fetchAll($this->model->getFilter(), $this->model->getOrder());

        $routes = array();
        foreach ($routesData as $routeData) {
            $routes[] = Model\Definition::getById($routeData['id']);
        }

        $this->model->setDefinitions($routes);

        return $routes;
    }

    /**
     * get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        $data = $this->db->fetchAll($this->model->getFilter(), $this->model->getOrder());
        $amount = count($data);

        return $amount;
    }
}
