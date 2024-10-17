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

namespace Instride\Bundle\DataDefinitionsBundle\Form\DataMapper;

use Instride\Bundle\DataDefinitionsBundle\Model\MappingInterface;
use Symfony\Component\Form\DataMapperInterface;

final class DefinitionMappingDataMapper implements DataMapperInterface
{
    public function mapDataToForms($data, $forms): void
    {
    }

    public function mapFormsToData($forms, &$data): void
    {
        $actualData = [];

        foreach ($forms as $key => $form) {
            $formData = $form->getData();

            if (!$formData instanceof MappingInterface) {
                continue;
            }

            $actualData[] = $formData;
        }

        $data = $actualData;
    }
}
