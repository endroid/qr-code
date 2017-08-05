<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Bundle\QrCodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WriterRegistryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('endroid.qrcode.writer_registry')) {
            return;
        }

        $writerRegistryDefinition = $container->findDefinition('endroid.qrcode.writer_registry');

        $taggedServices = $container->findTaggedServiceIds('endroid.qrcode.writer');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $writerRegistryDefinition->addMethodCall('addWriter', [new Reference($id), isset($attributes['set_as_default']) && $attributes['set_as_default']]);
            }
        }
    }
}
