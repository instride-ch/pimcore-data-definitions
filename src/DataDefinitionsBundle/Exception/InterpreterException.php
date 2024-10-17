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

namespace Instride\Bundle\DataDefinitionsBundle\Exception;

use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\MappingInterface;
use RuntimeException;
use Throwable;

class InterpreterException extends RuntimeException
{
    private function __construct(
        DataDefinitionInterface $definition,
        MappingInterface $mapping,
        array $params,
        $value,
        ?Throwable $previous = null,
    ) {
        parent::__construct($this->formatMessage($definition, $mapping, $params, $value, $previous), 0, $previous);
    }

    public static function fromInterpreter(
        DataDefinitionInterface $definition,
        MappingInterface $mapping,
        array $params,
        $value,
        ?Throwable $previous = null,
    ): self {
        return new self($definition, $mapping, $params, $value, $previous);
    }

    private function formatMessage(
        DataDefinitionInterface $definition,
        MappingInterface $mapping,
        array $params,
        $value,
        ?Throwable $previous = null,
    ): string {
        $format = '%1$s, %2$s';
        if ($previous !== null) {
            $format = '%1$s, %2$s: %3$s';
        }

        return sprintf(
            $format,
            $this->formatDefinition($definition),
            $this->formatSource($mapping, $value, $params['row'] ?? null),
            $previous ? $previous->getMessage() : null,
        );
    }

    private function formatDefinition(DataDefinitionInterface $definition): string
    {
        return sprintf('%1$s (ID %2$d)', $definition->getName(), $definition->getId());
    }

    private function formatSource(MappingInterface $mapping, $value, ?int $row = null): string
    {
        $format = 'from "%1$s" to "%2$s" (using interpreter "%3$s", config %4$s), got value %6$s';
        if ($row !== null) {
            $format = 'from "%1$s" (row %5$d) to "%2$s" (interpreter "%3$s", config %4$s), got value %6$s';
        }

        return sprintf(
            $format,
            $mapping->getFromColumn(),
            $mapping->getToColumn(),
            $mapping->getInterpreter(),
            var_export_pretty($mapping->getInterpreterConfig()),
            $row,
            $this->formatValue($value),
        );
    }

    private function formatValue($value): string
    {
        return var_export_pretty($value);
    }
}
