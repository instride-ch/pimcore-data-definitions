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

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\Element\ElementInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;

class LinkInterpreter implements InterpreterInterface
{
    public function interpret(
        Concrete $object,
        $value,
        MappingInterface $map,
        $data,
        DataDefinitionInterface $definition,
        $params,
        $configuration
    ) {
        if (($definition instanceof ExportDefinitionInterface) && $value instanceof Link) {
            return $value->getHref();
        }

        if (($definition instanceof ImportDefinitionInterface)) {
            $link = new Link();

            if (filter_var($value, FILTER_VALIDATE_URL)) {
                $link->setDirect($value);
            }

            $link->setText($value);

            if ($value instanceof ElementInterface) {
                $link->setElement($value);
            }

            return $link;
        }

        return null;
    }
}
