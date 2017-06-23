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
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\ImportDefinitionsBundle\Provider;

use Wvision\Bundle\ImportDefinitionsBundle\Model\Mapping\FromColumn;

class Json implements ProviderInterface
{
    /**
     * @var string
     */
    public $jsonExample;

    /**
     * @return string
     */
    public function getJsonExample()
    {
        return $this->jsonExample;
    }

    /**
     * @param string $jsonExample
     */
    public function setJsonExample($jsonExample)
    {
        $this->jsonExample = $jsonExample;
    }

    /**
     * Calculate depth
     *
     * @param array $arr
     * @return int
     */
    protected function getJsonDepth(array $arr)
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
        $depth = 0;
        foreach ($it as $v) {
            $it->getDepth() > $depth and $depth = $it->getDepth();
        }
        return $depth;
    }

    /**
     * {@inheritdoc}
     */
    public function testData()
    {
        return $this->getJsonDepth(json_decode($this->getJsonExample(), true)) === 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        $rows = json_decode($this->getJsonExample(), true);
        $returnHeaders = [];

        if (count($rows) > 0) {
            $firstRow = $rows[0];

            foreach ($firstRow as $key => $val) {
                $headerObj = new FromColumn();
                $headerObj->setIdentifier($key);
                $headerObj->setLabel($key);

                $returnHeaders[] = $headerObj;
            }
        }

        return $returnHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($definition, $params, $filter = null)
    {
        $file = PIMCORE_PROJECT_ROOT . "/" . $params['file'];

        if(file_exists($file)) {
            $json = file_get_contents($file);
            $data = json_decode($json, true);

            return $data;
        }
        else {
            $this->getLogger()->alert("file not found . $file");
        }

        return array();
    }
}
