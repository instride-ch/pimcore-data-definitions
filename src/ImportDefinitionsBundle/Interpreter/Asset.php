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

namespace ImportDefinitionsBundle\Interpreter;

use ImportDefinitionsBundle\Model\DefinitionInterface;
use ImportDefinitionsBundle\Model\Mapping;
use Pimcore\Model\DataObject\Concrete;
use ImportDefinitionsBundle\Service\Placeholder;

/**
 * Class Asset
 *
 * @package ImportDefinitionsBundle\Interpreter
 */
class Asset implements InterpreterInterface
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
     * @param Concrete $object
     * @param string $value
     * @param Mapping $map
     * @param array $data
     * @param DefinitionInterface $definition
     * @param array $params
     * @param array $configuration
     * @return null|\Pimcore\Model\Asset
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
        $asset = \Pimcore\Model\Asset::getByPath($assetFullPath);

        if (!$asset) {
            return null;
        }

        return $asset;
    }
}
