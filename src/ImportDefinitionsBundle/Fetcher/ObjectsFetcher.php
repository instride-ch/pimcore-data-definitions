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

namespace ImportDefinitionsBundle\Fetcher;

use ImportDefinitionsBundle\Model\ExportDefinitionInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;

class ObjectsFetcher implements FetcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetch(ExportDefinitionInterface $definition, $params, int $limit, int $offset, array $configuration)
    {
        $list = $this->getClassListing($definition, $params);
        $list->setLimit($limit);
        $list->setOffset($offset);

        return $list->load();
    }

    /**
     * {@inheritdoc}
     */
    public function count(ExportDefinitionInterface $definition, $params, array $configuration): int
    {
        return $this->getClassListing($definition, $params)->getTotalCount();
    }

    /**
     * @param ExportDefinitionInterface $definition
     * @return Listing
     */
    private function getClassListing(ExportDefinitionInterface $definition, $params)
    {
        $class = $definition->getClass();
        $classDefinition = ClassDefinition::getByName($class);
        $obj = null;

        if (!$classDefinition instanceof ClassDefinition) {
            throw new \InvalidArgumentException(sprintf('Class not found %s', $class));
        }

        $classList = '\Pimcore\Model\DataObject\\'.ucfirst($class).'\Listing';
        $list = new $classList;
        $list->setUnpublished($definition->isFetchUnpublished());

        if (isset($params['root'])) {
            $rootNode = Concrete::getById($params['root']);

            if (null !== $rootNode) {
                $list->addConditionParam('o_path LIKE :path', ['path' => $rootNode->getFullPath().'%']);
            }
        }

        return $list;
    }
}
