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
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Resource\Repository\PimcoreDaoRepositoryInterface;
use Wvision\Bundle\DataDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;

final class ImportDefinitionContext implements Context
{
    private $sharedStorage;
    private $definitionRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        PimcoreDaoRepositoryInterface $definitionRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->definitionRepository = $definitionRepository;
    }

    /**
     * @Transform /^import-definition "([^"]+)"$/
     */
    public function definitionWithName($name)
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
    public function definition()
    {
        return $this->sharedStorage->get('import-definition');
    }
}
