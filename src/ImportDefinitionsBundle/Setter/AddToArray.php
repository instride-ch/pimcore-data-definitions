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
 * @copyright  Copyright (c) 2017 Divante (http://www.divante.co)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Setter;

use ImportDefinitionsBundle\Model\Mapping;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class AddToArray
 *
 * @package ImportDefinitionsBundle\Setter
 */
class AddToArray implements SetterInterface
{
    /**
     * @param Concrete $object
     * @param mixed $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function set(Concrete $object, $value, Mapping $map, $data)
    {
        $setter = "set" . ucfirst($map->toColumn);
        $getter = "get" . ucfirst($map->toColumn);

        $existing = $object->$getter();

        $existing[] = $value;

        $object->$setter($existing);
    }
}
