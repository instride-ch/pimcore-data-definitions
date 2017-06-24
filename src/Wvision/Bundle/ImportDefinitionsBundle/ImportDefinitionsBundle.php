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

namespace Wvision\Bundle\ImportDefinitionsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler\CleanerRegistryCompilerPass;
use Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler\FilterRegistryCompilerPass;
use Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler\InterpreterRegistryCompilerPass;
use Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler\ProviderRegistryCompilerPass;
use Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler\RunnerRegistryCompilerPass;
use Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler\SetterRegistryCompilerPass;

class ImportDefinitionsBundle extends AbstractPimcoreBundle
{
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
        return 'Import Definitions allows you to create reusable Definitions for Importing all kinds of files';
    }

    public function getVersion()
    {
        return '2.0.0';
    }

    public function getInstaller()
    {
        parent::getInstaller();
    }

    /**
     * {@inheritdoc}
     */
    public function getJsPaths()
    {
        $jsFiles = [];

        if ($this->container->hasParameter('import_definitions.pimcore.admin.js')) {
            $jsFiles = array_merge(
                $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter('core_shop_resource.pimcore.admin.js')),
                $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter('import_definitions.pimcore.admin.js'))
            );
        }

        return $jsFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getCssPaths()
    {
        $cssFiles = [];

        if ($this->container->hasParameter('import_definitions.pimcore.admin.css')) {
            $cssFiles = $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter('import_definitions.pimcore.admin.css'));
        }

        return $cssFiles;
    }
}
