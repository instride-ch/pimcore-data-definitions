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

use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class ExportDefinitionEvent extends Event implements DefinitionEventInterface
{
    protected ExportDefinitionInterface $definition;

    protected $subject;

    protected array $params = [];

    public function __construct(
        ExportDefinitionInterface $definition,
        $subject = null,
        array $params = [],
    ) {
        $this->definition = $definition;
        $this->subject = $subject;
        $this->params = $params;
    }

    public function getDefinition(): ExportDefinitionInterface
    {
        return $this->definition;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
