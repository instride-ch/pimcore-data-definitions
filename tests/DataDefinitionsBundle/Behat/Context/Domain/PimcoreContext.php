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

namespace Wvision\Bundle\DataDefinitionsBundle\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Wvision\Bundle\DataDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Webmozart\Assert\Assert;

final class PimcoreContext implements Context
{
    private $sharedStorage;

    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^there should be "([^"]+)" data-objects for (definition)$/
     * @Given /^there should be "([^"]+)" data-objects for (class "[^"]+")$/
     */
    public function thereAreDataObjects(int $count, ClassDefinition $definition)
    {
        $fqcn = 'Pimcore\Model\DataObject\\'.ucfirst($definition->getName());

        /**
         * @var DataObject\Listing $list
         */
        $list = $fqcn::getList();

        Assert::eq($count, $list->getTotalCount(),
            sprintf(
                'Expected to have only %s DataObjects of type %s, but got %s instead',
                $count,
                $definition->getName(),
                $list->getTotalCount()
            )
        );
    }

    /**
     * @Given /^there should be "([^"]+)" unpublished data-objects for (definition)$/
     * @Given /^there should be "([^"]+)" unpublished data-objects for (class "[^"]+")$/
     */
    public function thereAreUnpublishedDataObjects(int $count, ClassDefinition $definition)
    {
        $fqcn = 'Pimcore\Model\DataObject\\'.ucfirst($definition->getName());

        /**
         * @var DataObject\Listing $list
         */
        $list = $fqcn::getList();
        $list->setCondition('published=0');

        Assert::eq($count, $list->getTotalCount(),
            sprintf(
                'Expected to have only %s DataObjects of type %s, but got %s instead',
                $count,
                $definition->getName(),
                $list->getTotalCount()
            )
        );
    }

    /**
     * @Given /^there should be "([^"]+)" published data-objects for (definition)$/
     * @Given /^there should be "([^"]+)" published data-objects for (class "[^"]+")$/
     */
    public function thereArePublishedDataObjects(int $count, ClassDefinition $definition)
    {
        $fqcn = 'Pimcore\Model\DataObject\\'.ucfirst($definition->getName());

        /**
         * @var DataObject\Listing $list
         */
        $list = $fqcn::getList();
        $list->setCondition('published=1');

        Assert::eq($count, $list->getTotalCount(),
            sprintf(
                'Expected to have only %s DataObjects of type %s, but got %s instead',
                $count,
                $definition->getName(),
                $list->getTotalCount()
            )
        );
    }

    /**
     * @Given /^the field "([^"]+)" for (object of the definition) should have the value of (asset "([^"]+)")$/
     * @Given /^the field "([^"]+)" for (object of the definition) should have the value "([^"]+)"$/
     * @Given /^the field "([^"]+)" for (object of the definition) should have the value null$/
     *
     * @Given /^the field "([^"]+)" for (object of class "[^"]+") should have the value of (asset "([^"]+)")$/
     * @Given /^the field "([^"]+)" for (object of class "[^"]+") should have the value "([^"]+)"$/
     * @Given /^the field "([^"]+)" for (object of class "[^"]+") should have the value null$/
     */
    public function theFieldForObjectOfDefinitionShouldHaveTheValueOf($field, DataObject\Concrete $object, $value = null)
    {
        $actualValue = $object->getValueForFieldName($field);

        if ($value === 'false') {
            $value = false;
        }
        else if ($value === 'true') {
            $value = true;
        }
        else if ($value === 'null') {
            $value = null;
        }

        Assert::true(
            $actualValue === $value,
            sprintf('Expected value %s but is %s', $value, $actualValue)
        );
    }

    /**
     * @Given /^the field "([^"]+)" for (object of the definition) should be of type external-image$/
     */
    public function theFieldForObjectOfDefinitionShouldBeOfTypeExternalImage($field, DataObject\Concrete $object)
    {
        $actualValue = $object->getValueForFieldName($field);

        Assert::isInstanceOf($actualValue, DataObject\Data\ExternalImage::class);
    }

    /**
     * @Given /^the field "([^"]+)" for (object of the definition) should be of type link$/
     */
    public function theFieldForObjectOfDefinitionShouldBeOfTypeLink($field, DataObject\Concrete $object)
    {
        $actualValue = $object->getValueForFieldName($field);

        Assert::isInstanceOf($actualValue, DataObject\Data\Link::class);
    }
}
