<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\DependencyInjection\Compiler;

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
                throw new \InvalidArgumentException('Tagged Service `'.$id.'` needs to have `type` attributes.');
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }

        $container->setParameter($this->parameter, $map);
    }
}
