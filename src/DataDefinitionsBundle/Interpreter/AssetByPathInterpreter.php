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

namespace WVision\Bundle\DataDefinitionsBundle\Interpreter;

use WVision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;
use WVision\Bundle\DataDefinitionsBundle\Model\Mapping;
use WVision\Bundle\DataDefinitionsBundle\Model\MappingInterface;
use Pimcore\Model\DataObject\Concrete;
use WVision\Bundle\DataDefinitionsBundle\Service\Placeholder;

class AssetByPathInterpreter implements InterpreterInterface
{
    /**
     * @var Placeholder
     */
    protected $placeholderService;

    /**
     * @param Placeholder $placeholderService
     */
    public function __construct(Placeholder $placeholderService)
    {
        $this->placeholderService = $placeholderService;
    }

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
        $assetFullPath = $configuration['path'] . "/" . $value;

        return \Pimcore\Model\Asset::getByPath($assetFullPath);
    }
}

class_alias(AssetByPathInterpreter::class, 'ImportDefinitionsBundle\Interpreter\AssetByPathInterpreter');
