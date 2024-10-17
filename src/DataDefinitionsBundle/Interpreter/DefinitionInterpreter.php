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

namespace Instride\Bundle\DataDefinitionsBundle\Interpreter;

use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Importer\ImporterInterface;
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Instride\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;
use Pimcore\Model\DataObject;

class DefinitionInterpreter implements InterpreterInterface
{
    public function __construct(
        private readonly DefinitionRepository $definitionRepository,
        private readonly ImporterInterface $importer,
    ) {
    }

    public function interpret(InterpreterContextInterface $context): mixed
    {
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
