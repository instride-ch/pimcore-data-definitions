<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinition;

use Exception;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\Listing\CallableFilterListingInterface;
use Pimcore\Model\Listing\CallableOrderListingInterface;
use Pimcore\Model\Listing\Traits\FilterListingTrait;
use Pimcore\Model\Listing\Traits\OrderListingTrait;

/**
 * @method loadList()
 * @method getAllIds()
 */
class Listing extends AbstractModel implements CallableFilterListingInterface, CallableOrderListingInterface
{
    use FilterListingTrait;
    use OrderListingTrait;

    /**
     * Contains the results of the list.
     * They are all an instance of Configuration.
     */
    public ?array $definitions = null;

    /**
     * Get Configurations.
     *
     * @return ExportDefinitionInterface[]
     *
     * @throws Exception
     */
    public function getObjects()
    {
        if (null === $this->definitions) {
            $this->loadList();
        }

        return $this->definitions;
    }

    /**
     * Set Definitions.
     */
    public function setObjects(array $definitions): void
    {
        $this->definitions = $definitions;
    }
}
