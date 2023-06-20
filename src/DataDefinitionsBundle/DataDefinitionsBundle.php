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

namespace Wvision\Bundle\DataDefinitionsBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\CoreShopRuleBundle;
use Pimcore\Bundle\AdminBundle\PimcoreAdminBundle;
use Pimcore\Bundle\SimpleBackendSearchBundle\PimcoreSimpleBackendSearchBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\CleanerRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ExportProviderRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ExportRunnerRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\FetcherRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\FilterRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\GetterRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ImportRuleActionPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ImportRuleConditionPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\InterpreterRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\LoaderRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\PersisterRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ProviderRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\RunnerRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\SetterRegistryCompilerPass;

class DataDefinitionsBundle extends AbstractResourceBundle
{
    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);

        $collection->addBundles([
            new PimcoreAdminBundle(),
            new PimcoreSimpleBackendSearchBundle()
        ]);

        $collection->addBundles([
            new CoreShopRuleBundle(),
        ], 3500);
    }

    public function getSupportedDrivers(): array
    {
        return [
            CoreShopResourceBundle::DRIVER_PIMCORE,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CleanerRegistryCompilerPass());
        $container->addCompilerPass(new FilterRegistryCompilerPass());
        $container->addCompilerPass(new InterpreterRegistryCompilerPass());
        $container->addCompilerPass(new ProviderRegistryCompilerPass());
        $container->addCompilerPass(new RunnerRegistryCompilerPass());
        $container->addCompilerPass(new SetterRegistryCompilerPass());
        $container->addCompilerPass(new LoaderRegistryCompilerPass());
        $container->addCompilerPass(new GetterRegistryCompilerPass());
        $container->addCompilerPass(new FetcherRegistryCompilerPass());
        $container->addCompilerPass(new ExportProviderRegistryCompilerPass());
        $container->addCompilerPass(new ExportRunnerRegistryCompilerPass());
        $container->addCompilerPass(new ImportRuleConditionPass());
        $container->addCompilerPass(new ImportRuleActionPass());
        $container->addCompilerPass(new PersisterRegistryCompilerPass());
    }

    public function getVersion(): string
    {
        if (InstalledVersions::isInstalled('w-vision/data-definitions')) {
            return InstalledVersions::getVersion('w-vision/data-definitions');
        }

        return '';
    }

    public function getNiceName(): string
    {
        return 'Data Definitions';
    }

    public function getDescription(): string
    {
        return 'Data Definitions allows you to create reusable Definitions for Importing all kinds of data into DataObjects.';
    }

    public function getInstaller(): ?InstallerInterface
    {
        return $this->container->get(Installer::class);
    }

    public function getAdminIframePath(): ?string
    {
        return null;
    }

    public function getJsPaths(): array
    {
        return [];
    }

    public function getCssPaths(): array
    {
        return [];
    }

    public function getEditmodeJsPaths(): array
    {
        return [];
    }

    public function getEditmodeCssPaths(): array
    {
        return [];
    }
}
