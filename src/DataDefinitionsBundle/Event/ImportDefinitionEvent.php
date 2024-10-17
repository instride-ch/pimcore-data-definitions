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

namespace Instride\Bundle\DataDefinitionsBundle\Event;

use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class ImportDefinitionEvent extends Event implements DefinitionEventInterface
{
    protected ImportDefinitionInterface $definition;

    protected $subject;

    protected array $options;

    public function __construct(
        ImportDefinitionInterface $definition,
        $subject = null,
        array $options = [],
    ) {
        $this->definition = $definition;
        $this->subject = $subject;
        $this->options = $options;
    }

    public function getDefinition(): ImportDefinitionInterface
    {
        return $this->definition;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
