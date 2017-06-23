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
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\ImportDefinitionsBundle\Interpreter;

use Wvision\Bundle\ImportDefinitionsBundle\Model\DefinitionInterface;
use Wvision\Bundle\ImportDefinitionsBundle\Model\Mapping;
use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\Object\Concrete;
use Wvision\Bundle\ImportDefinitionsBundle\Service\Placeholder;

class AssetUrl implements InterpreterInterface
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
    public function interpret(Concrete $object, $value, Mapping $map, $data, DefinitionInterface $definition, $params)
    {
        $config = $map->getInterpreterConfig();
        $path = $config['path'];

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $filename = File::getValidFilename(basename($value));

            //Convert placeholder path

            $assetPath = $this->placeholderService->replace($path, $data);
            $assetFullPath = $assetPath . "/" . $filename;

            $asset = Asset::getByPath($assetFullPath);

            if (!$asset instanceof Asset) {
                //Download
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
