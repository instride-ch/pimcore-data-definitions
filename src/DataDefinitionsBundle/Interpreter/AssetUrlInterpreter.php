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

use Doctrine\DBAL\Query\QueryBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Pimcore\File;
use Pimcore\Http\Exception\ResponseException;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Tool as PimcoreTool;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataSetAwareInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataSetAwareTrait;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;

class AssetUrlInterpreter implements InterpreterInterface, DataSetAwareInterface
{
    use DataSetAwareTrait;

    protected const METADATA_ORIGIN_URL = 'origin_url';
    protected Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function interpret(
        Concrete $object,
        $value,
        MappingInterface $map,
        array $data,
        DataDefinitionInterface $definition,
        array $params,
        array $configuration
    ) {
        $path = $configuration['path'];

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $asset = null;
            $filename = $this->getFileName($value);

            if ($configuration['deduplicate_by_url']) {
                if ($asset = $this->getDuplicatedAsset($value)) {
                    $filename = $asset->getFilename();
                    $assetPath = $asset->getPath();
                } else {
                    $assetPath = $path;
                }
            }
            else {
                $assetPath = $path;
            }

            $parent = Asset\Service::createFolderByPath($assetPath);

            if (!$asset instanceof Asset) {
                // Download
                $fileData = $this->getFileContents($value);

                if ($fileData) {
                    $asset = Asset::create($parent->getId(), [
                        'filename' => $filename,
                        'data' => $fileData
                    ], false);
                    $asset->addMetadata(self::METADATA_ORIGIN_URL, 'input', $value);
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

    private function getFileName(string $url) : ?string
    {
        $filename = null;
        try {
            $response = $this->httpClient->request("HEAD", $url);
            $headers = $response->getHeaders();

            if (
                isset($headers["Content-Disposition"]) &&
                preg_match(
                    '/^.*?filename=(?<f>[^\s]+|\x22[^\x22]+\x22)\x3B?.*$/m',
                    current($headers["Content-Disposition"]),
                    $match
                )
            ) {
                $filename =  trim($match['f'], ' ";');
            }
        } catch (ResponseException $exception) {
        }

        if (!$filename) {
            $filename = File::getValidFilename(basename($url));
        }

        return $filename;
    }

    protected function getFileContents(string $value): ?string
    {
        try {
            $response = $this->httpClient->request('GET', $value);
        } catch (TransferException $ex) {
            $response = null;
        }

        if ($response && $response->getStatusCode() === 200) {
            return (string) $response->getBody();
        }

        return null;
    }

    private function getDuplicatedAsset(string $value) : ?Asset
    {
        $listing = new Asset\Listing();
        $listing->onCreateQueryBuilder(
            function (QueryBuilder $select) {
                $select->join('assets','assets_metadata', 'am', 'id = am.cid');
            }
        );
        $listing->addConditionParam('am.name = ?', static::METADATA_ORIGIN_URL);
        $listing->addConditionParam('am.data = ?', $value);
        $listing->setLimit(1);
        $listing->setOrder(['creationDate', 'desc']);

        $duplicatedAssets = $listing->getAssets();

        return empty($duplicatedAssets) === false ? $duplicatedAssets[0] : null;
    }
}
