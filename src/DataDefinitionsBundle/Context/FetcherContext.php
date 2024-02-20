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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Context;

use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;

class FetcherContext extends Context implements FetcherContextInterface
{
    public function __construct(
        ExportDefinitionInterface $definition,
        array $params,
        array $configuration,
    ) {
        parent::__construct($definition, $params, $configuration);
    }

    public function getDefinition(): ExportDefinitionInterface
    {
        /**
         * @var ExportDefinitionInterface $definition
         */
        $definition = $this->definition;

        return $definition;
    }
}
