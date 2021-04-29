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

use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\ComposerPackageBundleInterface;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\CoreShopRuleBundle;
use LogicException;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
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
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\ProviderRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\RunnerRegistryCompilerPass;
use Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler\SetterRegistryCompilerPass;

class DataDefinitionsBundle extends AbstractResourceBundle implements PimcoreBundleInterface, ComposerPackageBundleInterface
{
    use PackageVersionTrait;

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);

        $collection->addBundles([
            new CoreShopRuleBundle(),
        ], 3500);
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $extension = $this->createContainerExtension();

            if (null !== $extension) {
                if (!$extension instanceof ExtensionInterface) {
                    throw new LogicException(sprintf('Extension %s must implement Symfony\Component\DependencyInjection\Extension\ExtensionInterface.',
                        get_class($extension)));
                }

                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }

        if ($this->extension) {
            return $this->extension;
        }
    }

    public function getPackageName(): string
    {
        return 'w-vision/data-definitions';
    }

    public function getSupportedDrivers(): array
    {
        return [
            CoreShopResourceBundle::DRIVER_PIMCORE,
        ];
    }

    public function build(ContainerBuilder $builder): void
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
        $builder->addCompilerPass(new ImportRuleConditionPass());
        $builder->addCompilerPass(new ImportRuleActionPass());
    }

    public function getNiceName(): string
    {
        return 'Data Definitions';
    }

    public function getDescription(): string
    {
        return 'Data Definitions allows you to create reusable Definitions for Importing all kinds of data into DataObjects.';
    }

    protected function getComposerPackageName(): string
    {
        return 'w-vision/data-definitions';
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    public function getAdminIframePath()
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
