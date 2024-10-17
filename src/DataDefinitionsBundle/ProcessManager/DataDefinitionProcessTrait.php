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

use Pimcore\Tool\Admin;
use ProcessManagerBundle\Model\ExecutableInterface;

trait DataDefinitionProcessTrait
{
    private function runDefinition(string $type, ExecutableInterface $executable, array $params = []): int
    {
        $settings = $executable->getSettings();
        if (isset($settings['params'])) {
            $params = array_replace(json_decode($settings['params'], true), $params);
        }

        $currentUser = Admin::getCurrentUser();

        if ($currentUser && !isset($params['userId'])) {
            $params['userId'] = $currentUser->getId();
        }

        $settings['command'] = [
            $type,
            '-d',
            $settings['definition'],
            '-p',
            json_encode($params),
        ];

        $executable->setSettings($settings);

        return parent::run($executable, $params);
    }
}
