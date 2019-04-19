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
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle;

use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\ComposerPackageBundleInterface;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use ImportDefinitionsBundle\DependencyInjection\Compiler\CleanerRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\ExportProviderRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\ExportRunnerRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\FetcherRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\FilterRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\GetterRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\InterpreterRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\LoaderRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\ProviderRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\RunnerRegistryCompilerPass;
use ImportDefinitionsBundle\DependencyInjection\Compiler\SetterRegistryCompilerPass;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ImportDefinitionsBundle extends AbstractResourceBundle implements PimcoreBundleInterface, ComposerPackageBundleInterface
{
    use PackageVersionTrait;

    /**
     * {@inheritdoc}
     */
    public function getPackageName()
    {
        return 'w-vision/import-definitions';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            CoreShopResourceBundle::DRIVER_PIMCORE,
        ];
    }

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
        $builder->addCompilerPass(new LoaderRegistryCompilerPass());
        $builder->addCompilerPass(new GetterRegistryCompilerPass());
        $builder->addCompilerPass(new FetcherRegistryCompilerPass());
        $builder->addCompilerPass(new ExportProviderRegistryCompilerPass());
        $builder->addCompilerPass(new ExportRunnerRegistryCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getNiceName()
    {
        return 'Import Definitions';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Import Definitions allows you to create reusable Definitions for Importing all kinds of data into DataObjects.';
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName()
    {
        return 'w-vision/import-definitions';
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminIframePath()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsPaths()
    {
        return [
            "/bundles/importdefinitions/pimcore/js/automap/fuse.min.js"
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCssPaths()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getEditmodeJsPaths()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getEditmodeCssPaths()
    {
        return [];
    }
}
