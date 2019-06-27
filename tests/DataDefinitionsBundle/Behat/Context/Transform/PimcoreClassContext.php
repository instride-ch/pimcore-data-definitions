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
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Pimcore\DataObject\ClassLoader;
use Wvision\Bundle\DataDefinitionsBundle\Behat\Service\ClassStorageInterface;
use Wvision\Bundle\DataDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Pimcore\Cache\Runtime;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Webmozart\Assert\Assert;

final class PimcoreClassContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ClassStorageInterface
     */
    private $classStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ClassStorageInterface  $classStorage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ClassStorageInterface $classStorage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->classStorage = $classStorage;
    }

    /**
     * @Transform /^class "([^"]+)"$/
     */
    public function class($name)
    {
        Runtime::clear();

        $name = $this->classStorage->get($name);

        ClassLoader::forceLoadDataObjectClass($name);

        $classDefinition = ClassDefinition::getByName($name);

        Assert::notNull($classDefinition, sprintf('Class Definition for class with name %s not found', $name));

        return $classDefinition;
    }

    /**
     * @Transform /^field-collection "([^"]+)"$/
     */
    public function fieldCollection($name)
    {
        $name = $this->classStorage->get($name);

        $definition = Definition::getByKey($name);

        Assert::notNull($definition, sprintf('Definition for fieldcollection with key %s not found', $name));

        return $definition;
    }

    /**
     * @Transform /^object-instance$/
     */
    public function objectInstance()
    {
        return $this->sharedStorage->get('object-instance');
    }

    /**
     * @Transform /^object-instance "([^"]+)"$/
     */
    public function objectInstanceWithKey($key)
    {
        return Concrete::getByPath('/'.$key);
    }

    /**
     * @Transform /^object of the definition$/
     */
    public function objectOfTheDefinition()
    {
        $definition = $this->definition();

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
    public function objectOfTheClass($name)
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
    public function definition()
    {
        Runtime::clear();

        $name = $this->sharedStorage->get('pimcore_definition_name');
        $class = $this->sharedStorage->get('pimcore_definition_class');

        if ($class === ClassDefinition::class) {
            return ClassDefinition::getByName($this->classStorage->get($name));
        }

        return $class::getByKey($this->classStorage->get($name));
    }
}
