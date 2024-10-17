<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\ProcessManager;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Instride\Bundle\DataDefinitionsBundle\Event\DefinitionEventInterface;
use ProcessManagerBundle\ProcessManagerBundle;

final class ProcessManagerExportListener extends AbstractProcessManagerListener
{
    public const PROCESS_TYPE = 'export_definitions';

    public const PROCESS_NAME = 'Export Definitions';

    private $providerRegistry;

    public function setProviderRegistry(ServiceRegistryInterface $providerRegistry): void
    {
        $this->providerRegistry = $providerRegistry;
    }

    public function onFinishedEvent(DefinitionEventInterface $event): void
    {
        if (null !== $this->process) {
            if ($this->process->getStatus() == ProcessManagerBundle::STATUS_RUNNING) {
                $this->process->setProgress($this->process->getTotal());
                $this->process->setMessage($event->getSubject());
                $this->process->setStatus(ProcessManagerBundle::STATUS_COMPLETED);
                $this->process->setCompleted(time());
                $this->process->save();
            }
            $definition = $event->getDefinition();

            $this->processLogger->info($this->process, ImportDefinitionsReport::EVENT_FINISHED . $event->getSubject());

            $provider = $this->providerRegistry->get($definition->getProvider());

            if ($provider instanceof ArtifactGenerationProviderInterface) {
                if (method_exists($this->process, 'setArtifact')) {
                    $artifact = $provider->generateArtifact(
                        $definition->getConfiguration(),
                        $definition,
                        $event->getParams(),
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
