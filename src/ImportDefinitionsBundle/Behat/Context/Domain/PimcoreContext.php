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

namespace ImportDefinitionsBundle\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use ImportDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Webmozart\Assert\Assert;

final class PimcoreContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;


    /**
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^there should be "([^"]+)" data-objects for (definition)$/
     * @Given /^there is a import-definition "([^"]+)" for (definition "[^"]+")$/
     */
    public function thereIsAImportDefinition(int $count, ClassDefinition $definition)
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
}
