<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace WVision\Bundle\DataDefinitionsBundle\Cleaner;

use WVision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;

class Unpublisher extends AbstractCleaner
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function cleanup(DefinitionInterface $definition, $objects)
    {
        $notFoundObjects = $this->getObjectsToClean($definition, $objects);

        foreach ($notFoundObjects as $obj) {
            $obj->setPublished(false);
            $obj->save();
        }
    }
}

class_alias(Unpublisher::class, 'ImportDefinitionsBundle\Cleaner\Unpublisher');
