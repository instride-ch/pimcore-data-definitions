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

namespace Instride\Bundle\DataDefinitionsBundle\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Instride\Bundle\DataDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Webmozart\Assert\Assert;

final class PimcoreContext implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage
    )
    {

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
