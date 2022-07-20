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
use Pimcore\File;
use Pimcore\Model\Asset;
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class AssetUrlInterpreter implements InterpreterInterface
{
    protected const METADATA_ORIGIN_URL = 'origin_url';
    protected \Psr\Http\Client\ClientInterface $httpClient;
    protected \Psr\Http\Message\RequestFactoryInterface $requestFactory;

    public function __construct(
        \Psr\Http\Client\ClientInterface $httpClient,
        \Psr\Http\Message\RequestFactoryInterface $requestFactory
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    public function interpret(InterpreterContextInterface $context): mixed
    {
        $path = $context->getConfiguration()['path'];

        if (filter_var($context->getValue(), FILTER_VALIDATE_URL)) {
            $asset = null;
            $filename = $this->getFileName($context->getValue());

            if ($context->getConfiguration()['deduplicate_by_url']) {
                if ($asset = $this->getDuplicatedAsset($context->getValue())) {
                    $filename = $asset->getFilename();
                    $assetPath = $asset->getPath();
                } else {
                    $assetPath = $path;
                }
            } else {
                $assetPath = $path;
            }

            $parent = Asset\Service::createFolderByPath($assetPath);

            if (!$asset instanceof Asset) {
                // Download
                $fileData = $this->getFileContents($context->getValue());

                if ($fileData) {
                    $asset = Asset::create($parent->getId(), [
                        'filename' => $filename,
                        'data' => $fileData,
                    ], false);
                    $asset->addMetadata(self::METADATA_ORIGIN_URL, 'input', $context->getValue());
                    $asset->save();
                }
            } else {
                $save = false;

                if ($context->getConfiguration()['relocate_existing_objects'] && $asset->getParent() !== $parent) {
                    $asset->setParent($parent);
                    $save = true;
                }

                if ($context->getConfiguration()['rename_existing_objects'] && $asset->getFilename() !== $filename) {
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

    private function getFileName(string $url): ?string
    {
        $filename = null;
        try {
            $request = $this->requestFactory->createRequest('HEAD', $url);
            $response = $this->httpClient->sendRequest($request);
            $headers = $response->getHeaders();

            if (
                isset($headers["Content-Disposition"]) &&
                preg_match(
                    '/^.*?filename=(?<f>[^\s]+|\x22[^\x22]+\x22)\x3B?.*$/m',
                    current($headers["Content-Disposition"]),
                    $match
                )
            ) {
                $filename = trim($match['f'], ' ";');
            }
        } catch (\Psr\Http\Client\ClientExceptionInterface $exception) {
        }

        if (!$filename) {
            $filename = File::getValidFilename(basename($url));
        }

        return $filename;
    }

    protected function getFileContents(string $value): ?string
    {
        try {
            $request = $this->requestFactory->createRequest('GET', $value);
            $response = $this->httpClient->sendRequest($request);
        } catch (\Psr\Http\Client\ClientExceptionInterface $ex) {
            return null;
        }

        if ($response->getStatusCode() === 200) {
            return (string)$response->getBody();
        }

        return null;
    }

    private function getDuplicatedAsset(string $value): ?Asset
    {
        $listing = new Asset\Listing();
        $listing->onCreateQueryBuilder(
            function (QueryBuilder $select) {
                $select->join('assets', 'assets_metadata', 'am', 'id = am.cid');
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
