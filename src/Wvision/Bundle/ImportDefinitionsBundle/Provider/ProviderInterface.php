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

interface ProviderInterface
{
    /**
     * Test Data provided for this Provider
     *
     * @return boolean
     * @throws \Exception
     */
    public function testData();

    /**
     * Get Columns from data
     *
     * @return FromColumn[]
     */
    public function getColumns();

    /**
     * @param $definition
     * @param $params
     * @param null $filter
     *
     * @return array
     */
    public function getData($definition, $params, $filter = null);
}
