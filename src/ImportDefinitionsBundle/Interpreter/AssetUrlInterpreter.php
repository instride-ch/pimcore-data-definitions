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

use ImportDefinitionsBundle\Model\DataSetAwareInterface;
use ImportDefinitionsBundle\Model\DataSetAwareTrait;
use ImportDefinitionsBundle\Model\DefinitionInterface;
use ImportDefinitionsBundle\Model\Mapping;
use ImportDefinitionsBundle\PlaceholderContext;
use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use ImportDefinitionsBundle\Service\Placeholder;

class AssetUrlInterpreter implements InterpreterInterface, DataSetAwareInterface
{
    use DataSetAwareTrait;

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
     * @throws \Exception
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data, DefinitionInterface $definition, $params, $configuration)
    {
        $path = $configuration['path'];

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $filename = File::getValidFilename(basename($value));

            // Convert placeholder path
            $context = new PlaceholderContext($data, $object);
            $assetPath = $this->placeholderService->replace($path, $context);
            $assetFullPath = sprintf('%s/%s', $assetPath, $filename);

            $asset = Asset::getByPath($assetFullPath);

            if (!$asset instanceof Asset) {
                // Download
                $data = @file_get_contents($value);

                if ($data) {
                    $asset = new Asset();
                    $asset->setFilename($filename);
                    $asset->setParent(Asset\Service::createFolderByPath($assetPath));
                    $asset->setData($data);
                    $asset->save();
                }
            }
            
            return $asset;
        }

        return null;
    }
}
