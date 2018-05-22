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

namespace ImportDefinitionsBundle\Model\Definition;

use Pimcore\Model;
use ImportDefinitionsBundle\Model\DefinitionInterface;

class Listing extends Model\Listing\JsonListing
{
    /**
     * Contains the results of the list.
     * They are all an instance of Configuration.
     *
     * @var array
     */
    public $definitions;

    /**
     * Get Configurations.
     *
     * @return DefinitionInterface[]
     * @throws \Exception
     */
    public function getObjects()
    {
        if (null === $this->definitions) {
            $this->load();
        }

        return $this->definitions;
    }

    /**
     * Set Definitions.
     *
     * @param array $definitions
     */
    public function setObjects($definitions)
    {
        $this->definitions = $definitions;
    }
}
