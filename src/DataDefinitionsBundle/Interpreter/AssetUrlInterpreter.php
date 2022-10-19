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
    protected const METADATA_ORIGIN_HASH = 'origin_hash';
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
        $url = $context->getValue();

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException(sprintf('Provided asset URL value %1$s is not a valid URL', $url));
        }
        $parent = Asset\Service::createFolderByPath($path);
        $filename = $this->getFileName($url, $context->getConfiguration()['use_content_disposition'] ?? false);

        $asset = null;
        if ($context->getConfiguration()['deduplicate_by_url'] ?? false) {
            $asset = $this->deduplicateAssetByUrl($url);
        }

        $fileHash = null;
        $fileData = null;
        if ($asset === null) {
            $fileData = $this->getFileContents($url);
            $fileHash = md5($fileData);

            if ($context->getConfiguration()['deduplicate_by_hash'] ?? false) {
                $asset = $this->deduplicateAssetByHash($fileHash);
            }
        }

        if ($asset === null) {
            // asset doesn't exist already
            $asset = Asset::create($parent->getId(), [
                'filename' => $filename,
                'data' => $fileData,
            ], false);
        }

        $save = false;
        $currentUrl = $asset->getMetadata(self::METADATA_ORIGIN_URL) ?? '';
        if (strpos($currentUrl, $url) === false) {
            $url = $currentUrl ? $currentUrl .'|'. $url : $url;
            $asset->addMetadata(self::METADATA_ORIGIN_URL, 'input', $url);
            $save = true;
        }

        // $fileHash might not be available here if we deduplicated by URL
        if ($fileHash !== null && $asset->getMetadata(self::METADATA_ORIGIN_HASH) !== $fileHash) {
            $asset->addMetadata(self::METADATA_ORIGIN_HASH, 'input', $fileHash);
            $save = true;
        }
        if ($context->getConfiguration()['relocate_existing_objects'] ?? false && $asset->getParent() !== $parent) {
            $asset->setParent($parent);
            $save = true;
        }
        if ($context->getConfiguration()['rename_existing_objects'] ?? false && $asset->getFilename() !== $filename) {
            $asset->setFilename($filename);
            $save = true;
        }

        if ($save) {
            $asset->save();
        }

        return $asset;
    }

    private function getFileName(string $url, bool $useContentDisposition = false): ?string
    {
        $filename = null;
        if ($useContentDisposition) {
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
        }
        if ($filename === null) {
            $filename = basename($url);
        }

        return File::getValidFilename($filename);
    }

    protected function getFileContents(string $value): string
    {
        try {
            $request = $this->requestFactory->createRequest('GET', $value);
            $response = $this->httpClient->sendRequest($request);
        } catch (\Psr\Http\Client\ClientExceptionInterface $ex) {
            throw new \RuntimeException('Unable to download asset from URL '.$value);
        }

        if ($response->getStatusCode() === 200) {
            return (string)$response->getBody();
        }

        throw new \RuntimeException('Unable to download asset from URL '.$value);
    }

    private function deduplicateAssetByUrl(string $value): ?Asset
    {
        return $this->deduplicateAsset(self::METADATA_ORIGIN_URL, $value, true);
    }

    private function deduplicateAssetByHash(string $value): ?Asset
    {
        return $this->deduplicateAsset(self::METADATA_ORIGIN_HASH, $value);
    }

    private function deduplicateAsset(string $name, string $value, bool $fuzzy = false)
    {
        $listing = new Asset\Listing();
        $listing->onCreateQueryBuilder(
            function (QueryBuilder $select) {
                $select->join('assets', 'assets_metadata', 'am', 'id = am.cid');
            }
        );
        $listing->addConditionParam('am.name = ?', $name);
        if ($fuzzy) {
            $listing->addConditionParam('am.data LIKE ?', '%'. $value .'%');
        } else {
            $listing->addConditionParam('am.data = ?', $value);
        }
        $listing->setLimit(1);
        $listing->setOrder(['creationDate', 'desc']);

        $duplicatedAssets = $listing->getAssets();

        return empty($duplicatedAssets) === false ? $duplicatedAssets[0] : null;
    }
}
