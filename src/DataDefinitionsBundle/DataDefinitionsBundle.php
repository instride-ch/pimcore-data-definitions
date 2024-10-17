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

namespace Instride\Bundle\DataDefinitionsBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\CoreShopRuleBundle;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\CleanerRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ExportProviderRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ExportRunnerRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\FetcherRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\FilterRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\GetterRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ImportRuleActionPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ImportRuleConditionPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\InterpreterRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\LoaderRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\PersisterRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ProviderRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\RunnerRegistryCompilerPass;
use Instride\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\SetterRegistryCompilerPass;
use Pimcore\Bundle\AdminBundle\PimcoreAdminBundle;
use Pimcore\Bundle\SimpleBackendSearchBundle\PimcoreSimpleBackendSearchBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DataDefinitionsBundle extends AbstractResourceBundle
{
    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);

        $collection->addBundles([
            new PimcoreAdminBundle(),
            new PimcoreSimpleBackendSearchBundle(),
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
        if (InstalledVersions::isInstalled('instride/data-definitions')) {
            return InstalledVersions::getVersion('instride/data-definitions');
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
