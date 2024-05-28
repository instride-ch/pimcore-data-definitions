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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\ProcessManager;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use ProcessManagerBundle\Model\ExecutableInterface;
use ProcessManagerBundle\Process\ProcessStartupFormResolverInterface;
use Instride\Bundle\DataDefinitionsBundle\Form\Type\ProcessManager\ExportDefinitionObjectStartupForm;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;

final class ExportDefinitionStartupFormResolver implements ProcessStartupFormResolverInterface
{
    public function __construct(
        private readonly DefinitionRepository $definitionRepository
    )
    {
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
