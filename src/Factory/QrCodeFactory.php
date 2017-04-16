<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Factory;

use Endroid\QrCode\QrCode;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QrCodeFactory
{
    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;

    /**
     * @param array $defaults
     */
    public function __construct(array $defaults = [])
    {
        $defaults = array_merge($this->getAvailableOptions(), $defaults);
        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver->setDefaults($defaults);
    }

    /**
     * @param array $options
     * @return QrCode
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        $qrCode = new QrCode($options['text']);
        $qrCode
            ->setText($options['text'])
            ->setSize($options['size'])
            ->setQuietZone($options['quiet_zone'])
            ->setForegroundColor($options['foreground_color'])
            ->setBackgroundColor($options['background_color'])
            ->setEncoding($options['encoding'])
            ->setErrorCorrectionLevel($options['error_correction_level'])
            ->setLabel($options['label'], $options['label_font_size'], $options['label_font_path'])
        ;

        return $qrCode;
    }

    /**
     * @return array
     */
    public function getAvailableOptions()
    {
        $options = [
            'text' => null,
            'size' => null,
            'quiet_zone' => null,
            'foreground_color' => null,
            'background_color' => null,
            'encoding' => null,
            'error_correction_level' => null,
            'label' => null,
            'label_font_size' => null,
            'label_font_path' => null,
        ];

        return $options;
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->optionsResolver->resolve();
    }
}
