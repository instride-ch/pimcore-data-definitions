<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://www.instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Model;

use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinition\Listing as ExportListing;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinition\Listing as ImportListing;

trait IdGenerator
{
    private string $mainPath = 'var/config';

    public function getSuggestedId(ExportListing|ImportListing $listing): int
    {
        $ids = $listing->getAllIds();

        $maxNumber = 1;

        foreach ($ids as $id) {
            if ((int) $id >= $maxNumber) {
                $maxNumber = (int) $id + 1;
            }
        }

        return $maxNumber;
    }
}
