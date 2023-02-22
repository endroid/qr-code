<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

final class WebpResult extends AbstractGdResult
{
    public const RESULT_OPTION_QUALITY = 'result_quality';

    protected function initOptions(): void
    {
        if (!isset($this->options[self::RESULT_OPTION_QUALITY])) {
            $this->options[self::RESULT_OPTION_QUALITY] = 80;
        }
    }

    public function getString(): string
    {
        ob_start();
        imagewebp($this->image, quality: $this->options[self::RESULT_OPTION_QUALITY]);

        return (string) ob_get_clean();
    }

    public function getMimeType(): string
    {
        return 'image/webp';
    }
}
