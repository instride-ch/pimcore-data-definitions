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

namespace ImportDefinitionsBundle\ProcessManager;

use ImportDefinitionsBundle\Model\ExportDefinitionInterface;
use Pimcore\Model\Asset;

trait ArtifactProviderTrait
{
    /**
     * {@inheritdoc}
     */
    public function generateArtifact($configuration, ExportDefinitionInterface $definition, $params): Asset
    {
        if (!$params['artifact']) {
            return null;
        }

        $artifactPath = dirname($params['artifact']);
        $artifactName = basename($params['artifact']);
        $artifactPath = Asset\Service::createFolderByPath($artifactPath);

        $stream = $this->provideArtifactStream($configuration, $definition, $params);

        $artifact = new Asset\Document();
        $artifact->setFilename($artifactName);
        $artifact->setStream($stream);
        $artifact->setParent($artifactPath);
        $artifact->save();

        fclose($stream);

        return $artifact;
    }

    /**
     * @param                           $configuration
     * @param ExportDefinitionInterface $definition
     * @param                           $params
     */
    public abstract function provideArtifactStream($configuration, ExportDefinitionInterface $definition, $params);
}
