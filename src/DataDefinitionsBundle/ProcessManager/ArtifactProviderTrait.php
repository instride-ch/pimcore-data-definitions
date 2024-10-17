<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\ProcessManager;

use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Pimcore\Model\Asset;

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

    abstract public function provideArtifactStream(
        array $configuration,
        ExportDefinitionInterface $definition,
        array $params,
    );
}
