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

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter;

use Wvision\Bundle\DataDefinitionsBundle\Model\DataSetAwareInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataSetAwareTrait;
use Wvision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\Mapping;
use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Service\Placeholder;
use Pimcore\Tool as PimcoreTool;
use Wvision\Bundle\DataDefinitionsBundle\Service\PlaceholderContext;

class AssetUrlInterpreter implements InterpreterInterface, DataSetAwareInterface
{
    const METADATA_ORIGIN_URL = 'origin_url';

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
            $assetsUrlPrefix = PimcoreTool::getHostUrl() . str_replace(PIMCORE_WEB_ROOT, '', PIMCORE_ASSET_DIRECTORY);

            // check if URL seems to be pointing to our own asset URL
            $assetFullPath = str_replace($assetsUrlPrefix, '', $value);
            if (0 === strpos($value, $assetsUrlPrefix) && null !== $asset = Asset::getByPath($assetFullPath)) {
                $filename = $asset->getFilename();
                $assetPath = dirname($assetFullPath);
            } elseif ($configuration['deduplicate_by_url']) {
                $listing = new Asset\Listing();
                $listing->onCreateQuery(function (\Pimcore\Db\ZendCompatibility\QueryBuilder $select){
                    $select->join('assets_metadata AS am', 'id = am.cid', ['cid']);
                });
                $listing->addConditionParam('am.name = ?', self::METADATA_ORIGIN_URL);
                $listing->addConditionParam('am.data = ?', $value);
                $listing->setLimit(1);
                $listing->setOrder(['creationDate', 'desc']);

                $asset = $listing->current();
                if ($asset) {
                    $filename = $asset->getFilename();
                    $assetPath = $asset->getPath();
                }
            } else {
                // Convert placeholder path
                $context = new PlaceholderContext($data, $object);
                $assetPath = $this->placeholderService->replace($path, $context);
                $assetFullPath = sprintf('%s/%s', $assetPath, $filename);

                $asset = Asset::getByPath($assetFullPath);
            }

            $parent = Asset\Service::createFolderByPath($assetPath);

            if (!$asset instanceof Asset) {
                // Download
                $data = @file_get_contents($value);

                if ($data) {
                    $asset = new Asset();
                    $asset->setFilename($filename);
                    $asset->setParent($parent);
                    $asset->setData($data);
                    $asset->addMetadata(self::METADATA_ORIGIN_URL, 'text', $value);
                    $asset->save();
                }
            } else {
                $save = false;

                if ($configuration['relocate_existing_objects'] && $asset->getParent() !== $parent) {
                    $asset->setParent($parent);
                    $save = true;
                }

                if ($configuration['rename_existing_objects'] && $asset->getFilename() !== $filename) {
                    $asset->setFilename($filename);
                    $save = true;
                }

                if ($save) {
                    $asset->save();
                }
            }

            return $asset;
        }

        return null;
    }
}

class_alias(AssetUrlInterpreter::class, 'ImportDefinitionsBundle\Interpreter\AssetUrlInterpreter');
