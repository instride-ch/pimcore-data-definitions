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

namespace ImportDefinitionsBundle\Service;

use ImportDefinitionsBundle\PlaceholderContext;
use Pimcore\File;

final class Placeholder
{
    /**
     * @var \Pimcore\Placeholder
     */
    private $helper;

    /**
     * @param                    $placeholder
     * @param PlaceholderContext $context
     *
     * @return string
     */
    public function replace($placeholder, PlaceholderContext $context)
    {
        $myData = $context->toArray();

        foreach ($myData as &$d) {
            if (\is_string($d)) {
                $d = File::getValidFilename($d);
            }
        }

        unset($d);

        return $this->getHelper()->replacePlaceholders($placeholder, $myData);
    }

    /**
     * @return \Pimcore\Placeholder
     */
    private function getHelper()
    {
        if (null === $this->helper) {
            $this->helper = new \Pimcore\Placeholder();
        }

        return $this->helper;
    }
}
