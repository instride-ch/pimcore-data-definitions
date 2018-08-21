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

            // Convert placeholder path
            $context = new PlaceholderContext($data, $object);
            $assetPath = $this->placeholderService->replace($path, $context);
            $assetFullPath = sprintf('%s/%s', $assetPath, $filename);
            
            if ($configuration['deduplicate_by_url']) {
                $listing = new Asset\Listing();
                $listing->onCreateQuery(function (\Pimcore\Db\ZendCompatibility\QueryBuilder $select){
                    $select->join('assets_metadata AS am', 'id = am.cid', ['cid']);
                });
                $listing->addConditionParam('am.name = ?', self::METADATA_ORIGIN_URL);
                $listing->addConditionParam('am.data = ?', $value);
                $listing->setLimit(1);
                $listing->setOrder(['creationDate', 'desc']);

                $asset = $listing->current();
            } else {
                $asset = Asset::getByPath($assetFullPath);
            }

            if (!$asset instanceof Asset) {
                // Download
                $data = @file_get_contents($value);

                if ($data) {
                    $asset = new Asset();
                    $asset->setFilename($filename);
                    $asset->setParent(Asset\Service::createFolderByPath($assetPath));
                    $asset->setData($data);
                    $asset->addMetadata(self::METADATA_ORIGIN_URL, 'text', $value);
                    $asset->save();
                }
            }
            
            return $asset;
        }

        return null;
    }
}
