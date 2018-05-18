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

namespace ImportDefinitionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractServiceRegistryCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $registry;

    /**
     * @var string
     */
    protected $parameter;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @param string $registry
     * @param string $parameter
     * @param string $tag
     */
    public function __construct($registry, $parameter, $tag)
    {
        $this->registry = $registry;
        $this->parameter = $parameter;
        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->registry)) {
            return;
        }

        $registry = $container->getDefinition($this->registry);

        $map = [];
        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException(sprintf('Tagged Service `%s` needs to have `type` attributes.', $id));
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }

        $container->setParameter($this->parameter, $map);
    }
}
