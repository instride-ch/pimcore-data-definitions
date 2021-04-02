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

namespace Wvision\Bundle\DataDefinitionsBundle\ProcessManager;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use ProcessManagerBundle\ProcessManagerBundle;
use Wvision\Bundle\DataDefinitionsBundle\Event\DefinitionEventInterface;

final class ProcessManagerExportListener extends AbstractProcessManagerListener
{
    const PROCESS_TYPE = "export_definitions";
    const PROCESS_NAME = "Export Definitions";

    private $providerRegistry;

    public function setProviderRegistry(ServiceRegistryInterface $providerRegistry) : void
    {
        $this->providerRegistry = $providerRegistry;
    }

    public function onFinishedEvent(DefinitionEventInterface $event) : void
    {
        if (null !== $this->process) {
            if ($this->process->getStatus() == ProcessManagerBundle::STATUS_RUNNING) {
                $this->process->setStatus(ProcessManagerBundle::STATUS_COMPLETED);
                $this->process->save();
            }
            $definition = $event->getDefinition();

            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_FINISHED.$event->getSubject());

            $provider = $this->providerRegistry->get($definition->getProvider());

            if ($provider instanceof ArtifactGenerationProviderInterface) {
                if (method_exists($this->process, 'setArtifact')) {
                    $artifact = $provider->generateArtifact(
                        $definition->getConfiguration(),
                        $definition,
                        $event->getParams()
                    );

                    if (null === $artifact) {
                        return;
                    }

                    $this->process->setArtifact($artifact);
                    $this->process->save();
                }
            }
        }
    }
}


