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
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter;

use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\DataObject\Data\ObjectMetadata;

class MetadataInterpreter implements InterpreterInterface
{    
    /**
     * {@inheritdoc}
     */
    public function interpret(
        Concrete $object,
        $value,
        MappingInterface $map,
        $data,
        DataDefinitionInterface $definition,
        $params,
        $configuration
    ) {
        $class = "\\Pimcore\\Model\\DataObject\\Data\\" . $configuration['class'];
        $fieldname = $map->getToColumn();
        
        $metadata = $configuration['metadata'];
        $metadata = json_decode($metadata, true);
        if (!is_array($metadata)) {
            $metadata = [];
        }

        $elementMetadata = new $class($fieldname, array_keys($metadata), $value);
        foreach ($metadata as $metadataKey => $metadataValue) {
            $setter = 'set' . ucfirst($metadataKey);
            $elementMetadata->$setter($metadataValue);
        }        
        
        return $elementMetadata;
    }
}


