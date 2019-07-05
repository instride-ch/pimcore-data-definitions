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

use ProcessManagerBundle\Model\ExecutableInterface;
use ProcessManagerBundle\Process\Pimcore;

final class ExportDefinitionProcess extends Pimcore
{
    /**
     * {@inheritdoc}
     */
    public function run(ExecutableInterface $executable, array $params = null)
    {
        $settings = $executable->getSettings();
        if (isset($settings['params'])) {
            $settings['params'] = array_replace(json_decode($settings['params'], true), (array) $params);
        } else {
            $settings['params'] = (array) $params;
        }

        $settings['command'] = sprintf('export-definitions:export -d %s -p "%s"', $settings['definition'], addslashes(json_encode($settings['params'])));

        $executable->setSettings($settings);

        return parent::run($executable, $params);
    }
}

class_alias(ExportDefinitionProcess::class, 'ImportDefinitionsBundle\ProcessManager\ExportDefinitionProcess');
