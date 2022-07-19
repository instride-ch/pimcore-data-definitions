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

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter;

use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class MetadataInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        $class = "\\Pimcore\\Model\\DataObject\\Data\\".$context->getConfiguration()['class'];
        $fieldname = $context->getMapping()->getToColumn();

        $metadata = $context->getConfiguration()['metadata'];
        $metadata = json_decode($metadata, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($metadata)) {
            $metadata = [];
        }

        /** @var ElementMetadata|ObjectMetadata $elementMetadata */
        $elementMetadata = new $class($fieldname, array_keys($metadata), $context->getValue());
        foreach ($metadata as $metadataKey => $metadataValue) {
            $setter = 'set'.ucfirst($metadataKey);
            $elementMetadata->$setter($metadataValue);
        }

        return $elementMetadata;
    }
}
