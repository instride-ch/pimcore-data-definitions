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

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\DataDefinitionsBundle\Importer\ImporterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\MappingInterface;

class DefinitionInterpreter implements InterpreterInterface
{
    /**
     * @var RepositoryInterface
     */
    private $definitionRepository;

    /**
     * @var ImporterInterface
     */
    private $importer;

    /**
     * @param RepositoryInterface $definitionRepository
     * @param ImporterInterface   $importer
     */
    public function __construct(RepositoryInterface $definitionRepository, ImporterInterface $importer)
    {
        $this->definitionRepository = $definitionRepository;
        $this->importer = $importer;
    }

    /**
     * {@inheritdoc}
     */
    public function interpret(
        Concrete $object,
        $value,
        MappingInterface $map,
        $data,
        DataDefinitionInterface $definition,
        $params,
        $configuration
    ) {
        $subDefinition = $this->definitionRepository->find($configuration['definition']);

        if (!$subDefinition instanceof ImportDefinitionInterface) {
            return null;
        }

        $imported = $this->importer->doImport($subDefinition, ['data' => [$data], 'child' => true]);

        if (count($imported) === 1) {
            return DataObject::getById($imported[0]);
        }

        return array_map(function ($id) {
            return DataObject::getById($id);
        }, $imported);
    }
}


