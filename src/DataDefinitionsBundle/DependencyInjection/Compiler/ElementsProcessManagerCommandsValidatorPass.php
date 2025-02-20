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

namespace Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler;

use Elements\Bundle\ProcessManagerBundle\Service\CommandsValidator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ElementsProcessManagerCommandsValidatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(CommandsValidator::class)) {
            return;
        }

        $def = $container->getDefinition(CommandsValidator::class);
        $whitelist = $def->getArgument('$whiteList');

        $whitelist[] = 'data-definitions:import';

        $def->setArgument('$whiteList', $whitelist);
    }
}
