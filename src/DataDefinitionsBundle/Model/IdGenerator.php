<?php

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
            if ((int)$id >= $maxNumber) {
                $maxNumber = (int)$id + 1;
            }
        }

        return $maxNumber;
    }

}
