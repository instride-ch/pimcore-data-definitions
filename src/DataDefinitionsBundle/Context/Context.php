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

namespace Instride\Bundle\DataDefinitionsBundle\Context;

use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

abstract class Context implements ContextInterface
{
    public function __construct(
        protected DataDefinitionInterface $definition,
        protected array $params,
        protected array $configuration,
    ) {
    }

    public function getDefinition(): DataDefinitionInterface
    {
        return $this->definition;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function withConfiguration(array $configuration): self
    {
        $context = clone $this;
        $context->setConfiguration($configuration);

        return $context;
    }

    protected function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
