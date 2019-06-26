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

namespace WVision\Bundle\DataDefinitionsBundle\ProcessManager;

use ProcessManagerBundle\Model\ExecutableInterface;
use ProcessManagerBundle\Process\Pimcore;

final class ImportDefinitionProcess extends Pimcore
{
    /**
     * {@inheritdoc}
     */
    public function run(ExecutableInterface $executable, array $params = null)
    {
        $settings = $executable->getSettings();

        $settings['command'] = sprintf('import-definitions:import -d %s -p "%s"', $settings['definition'], addslashes($settings['params']));

        $executable->setSettings($settings);

        return parent::run($executable);
    }
}

class_alias(ImportDefinitionProcess::class, 'ImportDefinitionsBundle\ProcessManager\ImportDefinitionProcess');
