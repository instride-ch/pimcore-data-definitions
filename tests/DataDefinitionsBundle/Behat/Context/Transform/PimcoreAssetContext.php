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
use Instride\Bundle\DataDefinitionsBundle\Behat\Service\SharedStorageInterface;
use Pimcore\Model\Asset;

final class PimcoreAssetContext implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage
    ) {

    }

    /**
     * @Transform /^asset "([^"]+)"$/
     */
    public function objectInstanceWithKey(string $path): Asset
    {
        return Asset::getByPath($path);
    }
}
