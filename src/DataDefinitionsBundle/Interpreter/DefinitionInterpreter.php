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

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject;
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Importer\ImporterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

class DefinitionInterpreter implements InterpreterInterface
{
    private RepositoryInterface $definitionRepository;
    private ImporterInterface $importer;

    public function __construct(RepositoryInterface $definitionRepository, ImporterInterface $importer)
    {
        $this->definitionRepository = $definitionRepository;
        $this->importer = $importer;
    }

    public function interpret(InterpreterContextInterface $context): mixed {
        $subDefinition = $this->definitionRepository->find($context->getConfiguration()['definition']);

        if (!$subDefinition instanceof ImportDefinitionInterface) {
            return null;
        }

        $imported = $this->importer->doImport($subDefinition, ['data' => [$context->getDataRow()], 'child' => true]);

        if (count($imported) === 1) {
            return DataObject::getById($imported[0]);
        }

        return array_map(static function ($id) {
            return DataObject::getById($id);
        }, $imported);
    }
}
