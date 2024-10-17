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

namespace Instride\Bundle\DataDefinitionsBundle\Fetcher;

use Instride\Bundle\DataDefinitionsBundle\Context\FetcherContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use InvalidArgumentException;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Listing;

class ObjectsFetcher implements FetcherInterface
{
    protected Listing $list;

    public function fetch(FetcherContextInterface $context, int $limit, int $offset)
    {
        $list = $this->getClassListing($context->getDefinition(), $context->getParams());
        $list->setLimit($limit);
        $list->setOffset($offset);

        return $list->load();
    }

    public function count(FetcherContextInterface $context): int
    {
        return $this->getClassListing($context->getDefinition(), $context->getParams())->getTotalCount();
    }

    private function getClassListing(ExportDefinitionInterface $definition, array $params): Listing
    {
        if (isset($this->list)) {
            return $this->list;
        }

        $class = $definition->getClass();
        $classDefinition = ClassDefinition::getByName($class);
        if (!$classDefinition instanceof ClassDefinition) {
            throw new InvalidArgumentException(sprintf('Class not found %s', $class));
        }

        $classList = '\Pimcore\Model\DataObject\\' . ucfirst($class) . '\Listing';
        $list = new $classList();
        $list->setUnpublished($definition->isFetchUnpublished());

        $rootNode = null;
        $conditionFilters = [];
        if (isset($params['root'])) {
            $rootNode = AbstractObject::getById($params['root']);

            if (null !== $rootNode) {
                $quotedPath = $list->quote($rootNode->getRealFullPath());
                $quotedWildcardPath = $list->quote(str_replace('//', '/', $rootNode->getRealFullPath() . '/') . '%');
                $conditionFilters[] = '(path = ' . $quotedPath . ' OR path LIKE ' . $quotedWildcardPath . ')';
            }
        }

        if (isset($params['query'])) {
            $query = $this->filterQueryParam($params['query']);
            if (!empty($query)) {
                $conditionFilters[] = 'oo_id IN (SELECT id FROM search_backend_data WHERE MATCH (`data`,`properties`) AGAINST (' . $list->quote(
                    $query,
                ) . ' IN BOOLEAN MODE))';
            }
        }

        if (isset($params['only_direct_children']) && $params['only_direct_children'] == 'true' && null !== $rootNode) {
            $conditionFilters[] = 'parentId = ' . $rootNode->getId();
        }

        if (isset($params['condition'])) {
            $conditionFilters[] = '(' . $params['condition'] . ')';
        }
        if (isset($params['ids'])) {
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
        $list->setOrderKey('id');
        $list->setOrder('asc');

        return $this->list = $list;
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
