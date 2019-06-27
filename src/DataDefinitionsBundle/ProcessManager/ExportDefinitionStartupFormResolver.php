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

namespace Wvision\Bundle\DataDefinitionsBundle\ProcessManager;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Wvision\Bundle\DataDefinitionsBundle\Form\Type\ProcessManager\ExportDefinitionObjectStartupForm;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use ProcessManagerBundle\Model\ExecutableInterface;
use ProcessManagerBundle\Process\ProcessStartupFormResolverInterface;

final class ExportDefinitionStartupFormResolver implements ProcessStartupFormResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    private $definitionRepository;

    /**
     * @param RepositoryInterface $definitionRepository
     */
    public function __construct(RepositoryInterface $definitionRepository)
    {
        $this->definitionRepository = $definitionRepository;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function resolveFormType(ExecutableInterface $executable): ?string
    {
        return ExportDefinitionObjectStartupForm::class;
    }
}

class_alias(ExportDefinitionStartupFormResolver::class, 'ImportDefinitionsBundle\ProcessManager\ExportDefinitionStartupFormResolver');
