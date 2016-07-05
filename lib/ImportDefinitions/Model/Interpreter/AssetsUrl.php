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
 * @copyright  Copyright (c) 2016 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitions\Model\Interpreter;

use ImportDefinitions\Model\Mapping;
use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\Object\Concrete;
use Pimcore\Placeholder;
use Pimcore\Tool;

/**
 * Class AssetsUrl
 * @package ImportDefinitions\Model\Interpreter
 */
class AssetsUrl extends AssetUrl
{

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data)
    {
        $asset = parent::interpret($object, $value, $map, $data);

        if ($asset) {
            return [$asset];
        }

        return null;
    }
}
