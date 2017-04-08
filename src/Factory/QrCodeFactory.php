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
     * Creates a new instance.
     *
     * @param array $defaults
     */
    public function __construct(array $defaults = [])
    {
        $defaults = array_merge($this->getAvailableOptions(), $defaults);
        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver->setDefaults($defaults);
    }

    /**
     * Creates a QR code.
     *
     * @param array $options
     *
     * @return QrCode
     */
    public function createQrCode(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        $qrCode = new QrCode();

        if (isset($options['text']) && !is_null($options['text'])) {
            $qrCode->setText($options['text']);
        }

        if (isset($options['size']) && !is_null($options['size'])) {
            $qrCode->setSize($options['size']);
        }

        if (isset($options['quiet_zone']) && !is_null($options['quiet_zone'])) {
            $qrCode->setQuietZone($options['quiet_zone']);
        }

        if (isset($options['encoding']) && !is_null($options['encoding'])) {
            $qrCode->setEncoding($options['encoding']);
        }

        if (isset($options['error_correction_level']) && !is_null($options['error_correction_level'])) {
            $qrCode->setErrorCorrectionLevel($options['error_correction_level']);
        }

        if (isset($options['foreground_color']) && !is_null($options['foreground_color'])) {
            $qrCode->setForegroundColor($options['foreground_color']);
        }

        if (isset($options['background_color']) && !is_null($options['background_color'])) {
            $qrCode->setBackgroundColor($options['background_color']);
        }

        if (isset($options['label']) && !is_null($options['label'])) {
            $qrCode->setLabel($options['label'], $options['label_font_size'], $options['label_font_path']);
        }

        return $qrCode;
    }

    /**
     * Returns all available options.
     *
     * @return array
     */
    public function getAvailableOptions()
    {
        $options = [
            'text' => null,
            'size' => null,
            'encoding' => null,
            'error_correction_level' => null,
            'foreground_color' => null,
            'background_color' => null,
            'quiet_zone' => null,
            'label' => null,
            'label_font_size' => null,
            'label_font_path' => null,
        ];

        return $options;
    }

    /**
     * Returns the current defaults.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->optionsResolver->resolve();
    }
}
