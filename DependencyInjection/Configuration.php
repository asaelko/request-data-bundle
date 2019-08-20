<?php

namespace Bilyiv\RequestDataBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Vladyslav Bilyi <beliyvladislav@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('request_data');

        if (!\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder->root('request_data');
        }

        return $treeBuilder;
    }
}
