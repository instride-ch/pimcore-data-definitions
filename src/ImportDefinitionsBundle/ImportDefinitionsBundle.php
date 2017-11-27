<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2017 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ImportDefinitionsBundle\DependencyInjection\Compiler\CleanerRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\FilterRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\InterpreterRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\ProviderRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\RunnerRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\SetterRegistryCompilerPass;

class ImportDefinitionsBundle extends AbstractPimcoreBundle
{
     use PackageVersionTrait;

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new CleanerRegistryCompilerPass());
        $builder->addCompilerPass(new FilterRegistryCompilerPass());
        $builder->addCompilerPass(new InterpreterRegistryCompilerPass());
        $builder->addCompilerPass(new ProviderRegistryCompilerPass());
        $builder->addCompilerPass(new RunnerRegistryCompilerPass());
        $builder->addCompilerPass(new SetterRegistryCompilerPass());
    }

    public function getNiceName()
    {
        return 'Import Definitions';
    }

    public function getDescription()
    {
        return 'Import Definitions allows you to create reusable Definitions for Importing all kinds of data into DataObjects.';
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return 'w-vision/import-definitions';
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }
}
