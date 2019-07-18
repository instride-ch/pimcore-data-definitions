<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Filter;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;
use Webmozart\Assert\Assert;

final class NestedFilter implements FilterInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $filterRegistry;

    /**
     * @param ServiceRegistryInterface $filterRegistry
     */
    public function __construct(ServiceRegistryInterface $filterRegistry)
    {
        $this->filterRegistry = $filterRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(DefinitionInterface $definition, $data, $object, array $configuration)
    {
        Assert::keyExists($configuration, 'filters');
        Assert::isArray($configuration['filters'], 'Filter Config needs to be array');

        foreach ($configuration['filters'] as $filter) {
            /** @var FilterInterface $filter */
            $filter = $this->filterRegistry->get($filter['type']);
            if (!$filter->filter($definition, $data, $object)) {
                return false;
            }
        }

        return true;
    }
}
