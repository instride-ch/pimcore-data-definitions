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

namespace ImportDefinitionsBundle\Form\DataMapper;

use ImportDefinitionsBundle\Model\Mapping;
use Symfony\Component\Form\DataMapperInterface;

class DefinitionMappingDataMapper implements DataMapperInterface
{
    /**
     * @var DataMapperInterface
     */
    private $propertyPathDataMapper;

    /**
     * @param DataMapperInterface $propertyPathDataMapper
     */
    public function __construct(DataMapperInterface $propertyPathDataMapper)
    {
        $this->propertyPathDataMapper = $propertyPathDataMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms): void
    {
        $this->propertyPathDataMapper->mapDataToForms($data, $forms);
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data): void
    {
        foreach ($forms as $key => $form) {
            $formData = $form->getData();
            $found = false;

            if (!$formData instanceof Mapping) {
                continue;
            }

            foreach ($data as &$map) {
                if (!$map instanceof Mapping) {
                    continue;
                }

                if ($map->getToColumn() === $formData->getToColumn()) {
                    $map = $formData;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $data[] = $formData;
            }
        }
    }
}
