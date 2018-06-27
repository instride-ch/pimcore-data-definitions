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
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Interpreter;

use ImportDefinitionsBundle\Model\DefinitionInterface;
use ImportDefinitionsBundle\Model\Mapping;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;

class ObjectResolver implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpret(
        Concrete $object,
        $value,
        Mapping $map,
        $data,
        DefinitionInterface $definition,
        $params,
        $configuration
    ) {
        if (!$value) {
            return $value;
        }

        $class = 'Pimcore\Model\DataObject\\' . $configuration['class'];
        $lookup = 'getBy'. $configuration['field'];

        /**
         * @var Listing $listing
         */
        $listing = $class::$lookup($value);
        $listing->setUnpublished($configuration['match_unpublished']);
        $found = $listing->count();

        if ($found < 1 || $found > 1) {
            // too few or too many found
            return null;
        }

        return $listing->current();
    }
}
