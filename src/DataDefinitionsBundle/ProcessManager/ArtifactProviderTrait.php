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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\ProcessManager;

use Pimcore\Model\Asset;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;

trait ArtifactProviderTrait
{
    public function generateArtifact($configuration, ExportDefinitionInterface $definition, $params): ?Asset
    {
        if (!isset($params['artifact'])) {
            return null;
        }

        $artifactPath = dirname($params['artifact']);
        $artifactName = basename($params['artifact']);

        if ($artifactPath === '.') {
            $artifactPath = Asset::getById(1);
        } else {
            $artifactPath = Asset\Service::createFolderByPath($artifactPath);
        }

        $stream = $this->provideArtifactStream($configuration, $definition, $params);

        $artifact = new Asset\Document();
        $artifact->setFilename($artifactName);
        $artifact->setStream($stream);
        $artifact->setParent($artifactPath);
        $artifact->setFilename(Asset\Service::getUniqueKey($artifact));
        $artifact->save();

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $artifact;
    }

    /**
     * @param array $configuration
     * @param ExportDefinitionInterface $definition
     * @param array $params
     */
    abstract public function provideArtifactStream(
        array $configuration,
        ExportDefinitionInterface $definition,
        array $params
    );
}
