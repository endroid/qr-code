<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Bundle\QrCodeBundle\DependencyInjection;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Predis\Response\Error;
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
                    ->scalarNode('writer')->end()
                    ->integerNode('size')->min(0)->end()
                    ->integerNode('margin')->min(0)->end()
                    ->scalarNode('encoding')->defaultValue('UTF-8')->end()
                    ->scalarNode('error_correction_level')
                        ->validate()
                            ->ifNotInArray(ErrorCorrectionLevel::toArray())
                            ->thenInvalid('Invalid error correction level %s')
                        ->end()
                    ->end()
                    ->arrayNode('foreground_color')
                        ->children()
                            ->scalarNode('r')->isRequired()->end()
                            ->scalarNode('g')->isRequired()->end()
                            ->scalarNode('b')->isRequired()->end()
                        ->end()
                    ->end()
                    ->arrayNode('background_color')
                        ->children()
                            ->scalarNode('r')->isRequired()->end()
                            ->scalarNode('g')->isRequired()->end()
                            ->scalarNode('b')->isRequired()->end()
                        ->end()
                    ->end()
                    ->scalarNode('logo_path')->end()
                    ->integerNode('logo_width')->end()
                    ->scalarNode('label')->end()
                    ->integerNode('label_font_size')->end()
                    ->scalarNode('label_font_path')->end()
                    ->scalarNode('label_alignment')
                        ->validate()
                            ->ifNotInArray(LabelAlignment::toArray())
                            ->thenInvalid('Invalid label alignment %s')
                        ->end()
                    ->end()
                    ->arrayNode('label_margin')
                        ->children()
                            ->scalarNode('t')->end()
                            ->scalarNode('r')->end()
                            ->scalarNode('b')->end()
                            ->scalarNode('l')->end()
                        ->end()
                    ->end()
                    ->booleanNode('validate_result')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
