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

namespace Instride\Bundle\DataDefinitionsBundle\Messenger;

class ImportRowMessage
{
    private int $definitionId;

    private array $data;

    private array $params;

    public function __construct(
        int $definitionId,
        array $data,
        array $params,
    ) {
        $this->definitionId = $definitionId;
        $this->data = $data;
        $this->params = $params;
    }

    public function getDefinitionId(): int
    {
        return $this->definitionId;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
