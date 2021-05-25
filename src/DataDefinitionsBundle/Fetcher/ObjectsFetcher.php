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
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Fetcher;

use InvalidArgumentException;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;

class ObjectsFetcher implements FetcherInterface
{
    public function fetch(ExportDefinitionInterface $definition, array $params, int $limit, int $offset, array $configuration)
    {
        $list = $this->getClassListing($definition, $params);
        $list->setLimit($limit);
        $list->setOffset($offset);

        return $list->load();
    }

    public function count(ExportDefinitionInterface $definition, array $params, array $configuration): int
    {
        return $this->getClassListing($definition, $params)->getTotalCount();
    }

    private function getClassListing(ExportDefinitionInterface $definition, array $params)
    {
        $class = $definition->getClass();
        $classDefinition = ClassDefinition::getByName($class);
        if (!$classDefinition instanceof ClassDefinition) {
            throw new InvalidArgumentException(sprintf('Class not found %s', $class));
        }

        $classList = '\Pimcore\Model\DataObject\\'.ucfirst($class).'\Listing';
        $list = new $classList;
        $list->setUnpublished($definition->isFetchUnpublished());

        $rootNode = null;
        $conditionFilters = [];
        if (isset($params['root'])) {
            $rootNode = Concrete::getById($params['root']);

            if (null !== $rootNode) {
                $quotedPath = $list->quote($rootNode->getRealFullPath());
                $quotedWildcardPath = $list->quote(str_replace('//', '/', $rootNode->getRealFullPath() . '/') . '%');
                $conditionFilters[] = '(o_path = ' . $quotedPath . ' OR o_path LIKE ' . $quotedWildcardPath . ')';
            }
        }

        if ($params['query']) {
            $query = $this->filterQueryParam($params['query']);
            if (!empty($query)) {
                $conditionFilters[] = 'oo_id IN (SELECT id FROM search_backend_data WHERE MATCH (`data`,`properties`) AGAINST (' . $list->quote($query) . ' IN BOOLEAN MODE))';
            }
        }

        if ($params['only_direct_children'] == 'true' && null !== $rootNode) {
            $conditionFilters[] = 'o_parentId = ' . $rootNode->getId();
        }

        if ($params['condition']) {
            $conditionFilters[] = '(' . $params['condition'] . ')';
        }
        if ($params['ids']) {
            $quotedIds = [];
            foreach ($params['ids'] as $id) {
                $quotedIds[] = $list->quote($id);
            }
            if (!empty($quotedIds)) {
                $conditionFilters[] = 'oo_id IN (' . implode(',', $quotedIds) . ')';
            }
        }

        $list->setCondition(implode(' AND ', $conditionFilters));

        // ensure a stable sort across pages
        $list->setOrderKey('o_id');
        $list->setOrder('asc');

        return $list;
    }

    protected function filterQueryParam(string $query)
    {
        if ($query === '*') {
            $query = '';
        }

        $query = str_replace(['%', '@'], ['*', '#'], $query);
        $query = preg_replace("@([^ ])\-@", '$1 ', $query);

        $query = str_replace(['<', '>', '(', ')', '~'], ' ', $query);

        // it is not allowed to have * behind another *
        $query = preg_replace('#[*]+#', '*', $query);

        // no boolean operators at the end of the query
        $query = rtrim($query, '+- ');

        return $query;
    }
}
