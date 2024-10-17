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

namespace Instride\Bundle\DataDefinitionsBundle\ProcessManager;

use Instride\Bundle\DataDefinitionsBundle\Form\Type\ProcessManager\ExportDefinitionObjectStartupForm;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use ProcessManagerBundle\Model\ExecutableInterface;
use ProcessManagerBundle\Process\ProcessStartupFormResolverInterface;

final class ExportDefinitionStartupFormResolver implements ProcessStartupFormResolverInterface
{
    public function __construct(
        private readonly DefinitionRepository $definitionRepository,
    ) {
    }

    public function supports(ExecutableInterface $executable): bool
    {
        if ($executable->getType() !== 'exportdefinition') {
            return false;
        }

        $definition = $this->definitionRepository->find($executable->getSettings()['definition']);

        if (!$definition instanceof ExportDefinitionInterface) {
            return false;
        }

        if ($definition->getFetcher() !== 'objects') {
            return false;
        }

        return true;
    }

    public function resolveFormType(ExecutableInterface $executable): ?string
    {
        return ExportDefinitionObjectStartupForm::class;
    }
}
