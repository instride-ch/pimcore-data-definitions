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

namespace Wvision\Bundle\DataDefinitionsBundle\ProcessManager;

use ProcessManagerBundle\Model\ExecutableInterface;
use ProcessManagerBundle\Process\Pimcore;

final class ExportDefinitionProcess extends Pimcore
{
    public function run(ExecutableInterface $executable, array $params = null)
    {
        $settings = $executable->getSettings();
        if (isset($settings['params'])) {
            $settings['params'] = array_replace(json_decode($settings['params'], true), (array)$params);
        } else {
            $settings['params'] = (array)$params;
        }

        $settings['command'] = [
            'data-definitions:export',
            '-d',
            $settings['definition'],
            '-p',
            json_encode($settings['params']),
        ];

        $executable->setSettings($settings);

        return parent::run($executable, $params);
    }
}
