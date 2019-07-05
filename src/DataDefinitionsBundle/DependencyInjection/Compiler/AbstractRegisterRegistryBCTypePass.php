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

namespace Wvision\Bundle\DataDefinitionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractRegisterRegistryBCTypePass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $registry;

    /**
     * @var string
     */
    protected $formRegistry;

    /**
     * @var string
     */
    protected $parameter;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $bcParameter;

    /**
     * @var string
     */
    protected $bcTag;

    /**
     * RegisterRegistryBCTypePass constructor.
     * @param string $registry
     * @param string $formRegistry
     * @param string $parameter
     * @param string $tag
     * @param string $bcParameter
     * @param string $bcTag
     */
    public function __construct(
        string $registry,
        string $formRegistry,
        string $parameter,
        string $tag,
        string $bcParameter,
        string $bcTag
    ) {
        $this->registry = $registry;
        $this->formRegistry = $formRegistry;
        $this->parameter = $parameter;
        $this->tag = $tag;
        $this->bcParameter = $bcParameter;
        $this->bcTag = $bcTag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->registry) || !$container->has($this->formRegistry)) {
            return;
        }

        $registry = $container->getDefinition($this->registry);
        $formRegistry = $container->getDefinition($this->formRegistry);
        $doneIds = [];
        $map = [];
        foreach ([$this->tag, $this->bcTag] as $tag) {
            foreach ($container->findTaggedServiceIds($tag) as $id => $attributes) {
                if ($tag === $this->bcTag && in_array($id, $doneIds, true)) {
                    continue;
                }

                $definition = $container->findDefinition($id);

                if (!isset($attributes[0]['type'])) {
                    $attributes[0]['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
                }

                if ($tag === $this->bcTag) {
                    @trigger_error(
                        sprintf(
                            'Tag %s is deprecated and will be removed with 3.0.0, use %s instead.',
                            $this->bcTag,
                            $this->tag
                        ),
                        E_USER_DEPRECATED
                    );
                }

                $map[$attributes[0]['type']] = $attributes[0]['type'];

                $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);

                if (isset($attributes[0]['form-type'])) {
                    $formRegistry->addMethodCall('add',
                        [$attributes[0]['type'], 'default', $attributes[0]['form-type']]);
                }

                $doneIds[] = $id;
            }
        }

        foreach ([$this->parameter, $this->bcParameter] as $parameter) {
            $container->setParameter($parameter, $map);
        }
    }
}
