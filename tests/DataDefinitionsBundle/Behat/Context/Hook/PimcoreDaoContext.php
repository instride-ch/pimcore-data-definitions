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

namespace Instride\Bundle\DataDefinitionsBundle\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Pimcore\Cache;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\DataObject\Objectbrick;
use Symfony\Component\HttpKernel\KernelInterface;

final class PimcoreDaoContext implements Context
{
    public function __construct(
        private readonly Connection $connection,
        private readonly KernelInterface $kernel,
    )
    {

    }

    /**
     * @BeforeScenario
     */
    public function setKernel(): void
    {
        \Pimcore::setKernel($this->kernel);
    }

    /**
     * @BeforeScenario
     */
    public function purgeObjects(): void
    {
        Cache::clearAll();
        Cache\RuntimeCache::clear();

        /**
         * @var Listing $list
         */
        $list = new DataObject\Listing();
        $list->setUnpublished(true);
        $list->setCondition('id <> 1');
        $list->load();

        foreach ($list->getObjects() as $obj) {
            $obj->delete();
        }

        //Force
        $this->connection->executeQuery('DELETE FROM objects WHERE id <> 1');
    }

    /**
     * @BeforeScenario
     */
    public function purgeAssets(): void
    {
        /**
         * @var Asset\Listing $list
         */
        $list = new Asset\Listing();
        $list->setCondition('id <> 1');
        $list->load();

        foreach ($list->getAssets() as $asset) {
            $asset->delete();
        }
    }

    /**
     * @BeforeScenario
     */
    public function purgeIMLog()
    {
        $this->connection->executeQuery('TRUNCATE TABLE data_definitions_import_log');
    }

    /**
     * @BeforeScenario
     */
    public function purgeBricks(): void
    {
        $list = new Objectbrick\Definition\Listing();
        $list->load();

        foreach ($list->load() as $brick) {
            /**
             * @psalm-suppress DocblockTypeContradiction
             */
            if (!$brick instanceof Objectbrick\Definition) {
                continue;
            }

            if (str_starts_with($brick->getKey(), 'Behat')) {
                $brick->delete();
            }
        }
    }

    /**
     * @BeforeScenario
     */
    public function clearRuntimeCacheScenario()
    {
        //Clearing it here is totally fine, since each scenario has its own separated context of objects
        Cache\RuntimeCache::clear();
    }

    /**
     * @BeforeStep
     */
    public function clearRuntimeCacheStep(): void
    {
        //We should not clear Pimcore Objects here, otherwise we lose the reference to it
        //and end up having the same object twice
        $copy = \Pimcore\Cache\RuntimeCache::getInstance()->getArrayCopy();
        $keepItems = [];

        foreach ($copy as $key => $value) {
            if (str_starts_with($key, 'object_')) {
                $keepItems[] = $key;
            }
        }

        \Pimcore\Cache\RuntimeCache::clear($keepItems);
    }

    /**
     * @BeforeScenario
     */
    public function purgeClasses(): void
    {
        $list = new ClassDefinition\Listing();
        $list->setCondition('name LIKE ?', ['Behat%']);
        $list->load();

        foreach ($list->getClasses() as $class) {
            /**
             * @psalm-suppress DocblockTypeContradiction
             */
            if (!$class instanceof ClassDefinition) {
                continue;
            }

            $class->delete();
        }
    }

    /**
     * @BeforeScenario
     */
    public function purgeFieldCollections(): void
    {
        $list = new Fieldcollection\Definition\Listing();
        $list->load();

        foreach ($list->load() as $collection) {
            /**
             * @psalm-suppress DocblockTypeContradiction
             */
            if (!$collection instanceof Fieldcollection\Definition) {
                continue;
            }

            if (str_starts_with($collection->getKey(), 'Behat')) {
                $collection->delete();
            }
        }
    }
}
