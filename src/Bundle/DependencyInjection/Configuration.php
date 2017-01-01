<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder
            ->root('endroid_qr_code')
                ->children()
                    ->integerNode('size')->min(0)->defaultValue(200)->end()
                    ->integerNode('padding')->min(0)->end()
                    ->scalarNode('label')->end()
                    ->integerNode('label_font_size')->end()
                    ->scalarNode('label_font_path')->end()
                    ->scalarNode('extension')->end()
                    ->scalarNode('error_correction_level')
                        ->validate()
                            ->ifTrue(function ($value) {
                                return !defined('Endroid\QrCode\QrCode::LEVEL_'.strtoupper($value));
                            })
                            ->thenInvalid('Invalid error correction level "%s"')
                        ->end()
                    ->end()
                    ->arrayNode('foreground_color')
                        ->children()
                            ->scalarNode('r')->isRequired()->end()
                            ->scalarNode('g')->isRequired()->end()
                            ->scalarNode('b')->isRequired()->end()
                            ->scalarNode('a')->isRequired()->end()
                        ->end()
                    ->end()
                    ->arrayNode('background_color')
                        ->children()
                            ->scalarNode('r')->isRequired()->end()
                            ->scalarNode('g')->isRequired()->end()
                            ->scalarNode('b')->isRequired()->end()
                            ->scalarNode('a')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
