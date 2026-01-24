<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in LICENSE.md
 * which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    DDCL
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
