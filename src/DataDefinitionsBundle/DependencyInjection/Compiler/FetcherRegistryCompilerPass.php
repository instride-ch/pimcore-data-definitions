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

namespace WVision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler;

final class FetcherRegistryCompilerPass extends AbstractRegisterRegistryBCTypePass
{
    public function __construct()
    {
        parent::__construct(
            'data_definitions.registry.fetcher',
            'data_definitions.form.registry.fetcher',
            'data_definitions.fetchers',
            'data_definitions.fetcher',
            'import_definition.fetchers',
            'import_definition.fetcher'
        );
    }
}
