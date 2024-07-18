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
use Instride\Bundle\DataDefinitionsBundle\Behat\Service\ClassStorageInterface;
use Instride\Bundle\DataDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Webmozart\Assert\Assert;

final class PimcoreClassContext implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
        private readonly ClassStorageInterface $classStorage
    ) {
    }

    /**
     * @Transform /^class "([^"]+)"$/
     */
    public function class(string $name): ClassDefinition
    {
        RuntimeCache::clear();

        $name = $this->classStorage->get($name);

        $classDefinition = ClassDefinition::getByName($name);

        Assert::notNull($classDefinition, sprintf('Class Definition for class with name %s not found', $name));

        return $classDefinition;
    }

    /**
     * @Transform /^field-collection "([^"]+)"$/
     */
    public function fieldCollection(string $name): Definition
    {
        $name = $this->classStorage->get($name);

        $definition = Definition::getByKey($name);

        Assert::notNull($definition, sprintf('Definition for fieldcollection with key %s not found', $name));

        return $definition;
    }

    /**
     * @Transform /^object-instance$/
     */
    public function objectInstance(): Concrete
    {
        return $this->sharedStorage->get('object-instance');
    }

    /**
     * @Transform /^object-instance "([^"]+)"$/
     */
    public function objectInstanceWithKey(string $key): Concrete
    {
        return Concrete::getByPath('/'.$key);
    }

    /**
     * @Transform /^object of the definition$/
     */
    public function objectOfTheDefinition(): Concrete
    {
        $definition = $this->definition();

        /**
         * @var class-string $fqcn
         */
        $fqcn = 'Pimcore\Model\DataObject\\'.ucfirst($definition->getName());

        /**
         * @var DataObject\Listing $list
         */
        $list = $fqcn::getList();
        $list->setUnpublished(true);

        Assert::eq(1, $list->getTotalCount(), sprintf('Can only find one object, but the list contains more or none'));

        return $list->getObjects()[0];
    }

    /**
     * @Transform /^object of class "([^"]+)"$/
     */
    public function objectOfTheClass(string $name): Concrete
    {
        $definition = $this->class($name);

        $fqcn = 'Pimcore\Model\DataObject\\'.ucfirst($definition->getName());

        /**
         * @var DataObject\Listing $list
         */
        $list = $fqcn::getList();
        $list->setUnpublished(true);

        Assert::eq(1, $list->getTotalCount(), sprintf('Can only find one object, but the list contains more or none'));

        return $list->getObjects()[0];
    }

    /**
     * @Transform /^definition/
     * @Transform /^definitions/
     */
    public function definition(): ClassDefinition
    {
        RuntimeCache::clear();

        $name = $this->sharedStorage->get('pimcore_definition_name');
        $class = $this->sharedStorage->get('pimcore_definition_class');

        if ($class === ClassDefinition::class) {
            return ClassDefinition::getByName($this->classStorage->get($name));
        }

        return $class::getByKey($this->classStorage->get($name));
    }
}
