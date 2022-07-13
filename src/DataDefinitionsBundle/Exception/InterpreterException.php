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

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Exception;

use RuntimeException;
use Throwable;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;

class InterpreterException extends RuntimeException
{
    private function __construct(
        DataDefinitionInterface $definition,
        MappingInterface $mapping,
        array $params,
        $value,
        ?Throwable $previous = null
    ) {
        parent::__construct($this->formatMessage($definition, $mapping, $params, $value, $previous), 0, $previous);
    }

    public static function fromInterpreter(
        DataDefinitionInterface $definition,
        MappingInterface $mapping,
        array $params,
        $value,
        ?Throwable $previous = null
    ): InterpreterException {
        return new self($definition, $mapping, $params, $value, $previous);
    }

    private function formatMessage(
        DataDefinitionInterface $definition,
        MappingInterface $mapping,
        array $params,
        $value,
        ?Throwable $previous = null
    ): string {
        $format = '%1$s, %2$s';
        if ($previous !== null) {
            $format = '%1$s, %2$s: %3$s';
        }

        return sprintf(
            $format,
            $this->formatDefinition($definition),
            $this->formatSource($mapping, $value, $params['row'] ?? null),
            $previous ? $previous->getMessage() : null
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
            $this->formatValue($value)
        );
    }

    private function formatValue($value): string
    {
        return var_export_pretty($value);
    }
}
