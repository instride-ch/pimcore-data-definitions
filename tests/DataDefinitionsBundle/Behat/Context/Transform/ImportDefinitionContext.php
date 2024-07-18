<?php
/**
 * Import Definitions.
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

namespace Instride\Bundle\DataDefinitionsBundle\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Resource\Repository\PimcoreDaoRepositoryInterface;
use Instride\Bundle\DataDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

final class ImportDefinitionContext implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
        private readonly PimcoreDaoRepositoryInterface $definitionRepository
    ) {
    }

    /**
     * @Transform /^import-definition "([^"]+)"$/
     */
    public function definitionWithName(string $name): DataDefinitionInterface
    {
        $all = $this->definitionRepository->findAll();

        /**
         * @var DataDefinitionInterface $definition
         */
        foreach ($all as $definition) {
            if ($definition->getName() === $name) {
                return $definition;
            }
        }

        throw new \InvalidArgumentException(sprintf('Definition with name %s not found', $name));
    }

    /**
     * @Transform /^import-definition$/
     * @Transform /^import-definitions$/
     */
    public function definition(): ?DataDefinitionInterface
    {
        return $this->sharedStorage->get('import-definition');
    }
}
