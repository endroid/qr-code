<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Factory;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class QrCodeFactory
{
    /**
     * @var array
     */
    private $definedOptions = [
        'size',
        'quiet_zone',
        'foreground_color',
        'background_color',
        'encoding',
        'error_correction_level',
        'label',
        'label_font_size',
        'label_font_path',
        'label_alignment',
        'label_margin',
        'logo_path',
        'logo_size',
        'validate_result'
    ];

    /**
     * @var array
     */
    private $defaultOptions;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param array $defaultOptions
     */
    public function __construct(array $defaultOptions = [])
    {
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * @param string $text
     * @param array $options
     * @return QrCode
     */
    public function create($text = '', array $options = [])
    {
        $options = $this->getOptionsResolver()->resolve($options);
        $accessor = PropertyAccess::createPropertyAccessor();

        $qrCode = new QrCode($text);
        foreach ($this->definedOptions as $option) {
            if (isset($options[$option])) {
                $accessor->setValue($qrCode, $option, $options[$option]);
            }
        }

        return $qrCode;
    }

    /**
     * @return OptionsResolver
     */
    private function getOptionsResolver()
    {
        if (!$this->optionsResolver instanceof OptionsResolver) {
            $this->optionsResolver = $this->createOptionsResolver();
        }

        return $this->optionsResolver;
    }

    /**
     * @return OptionsResolver
     */
    private function createOptionsResolver()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefaults($this->defaultOptions)
            ->setDefined($this->definedOptions)
        ;

        return $optionsResolver;
    }
}
