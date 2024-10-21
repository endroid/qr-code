<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Encoding\EncodingInterface;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Font\FontInterface;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Label\Margin\MarginInterface;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\ValidatingWriterInterface;
use Endroid\QrCode\Writer\WriterInterface;

final readonly class Builder implements BuilderInterface
{
    public function __construct(
        private WriterInterface $writer = new PngWriter(),
        /** @var array<mixed> */
        private array $writerOptions = [],
        private bool $validateResult = false,
        // QrCode options
        private string $data = '',
        private EncodingInterface $encoding = new Encoding('UTF-8'),
        private ErrorCorrectionLevel $errorCorrectionLevel = ErrorCorrectionLevel::Low,
        private int $size = 300,
        private int $margin = 10,
        private RoundBlockSizeMode $roundBlockSizeMode = RoundBlockSizeMode::Margin,
        private ColorInterface $foregroundColor = new Color(0, 0, 0),
        private ColorInterface $backgroundColor = new Color(255, 255, 255),
        // Label options
        private string $labelText = '',
        private FontInterface $labelFont = new Font(__DIR__.'/../../assets/open_sans.ttf', 16),
        private LabelAlignment $labelAlignment = LabelAlignment::Center,
        private MarginInterface $labelMargin = new Margin(0, 10, 10, 10),
        private ColorInterface $labelTextColor = new Color(0, 0, 0),
        // Logo options
        private string $logoPath = '',
        private ?int $logoResizeToWidth = null,
        private ?int $logoResizeToHeight = null,
        private bool $logoPunchoutBackground = false,
    ) {
    }

    /** @param array<mixed>|null $writerOptions */
    public function build(
        ?WriterInterface $writer = null,
        ?array $writerOptions = null,
        ?bool $validateResult = null,
        // QrCode options
        ?string $data = null,
        ?EncodingInterface $encoding = null,
        ?ErrorCorrectionLevel $errorCorrectionLevel = null,
        ?int $size = null,
        ?int $margin = null,
        ?RoundBlockSizeMode $roundBlockSizeMode = null,
        ?ColorInterface $foregroundColor = null,
        ?ColorInterface $backgroundColor = null,
        // Label options
        ?string $labelText = null,
        ?FontInterface $labelFont = null,
        ?LabelAlignment $labelAlignment = null,
        ?MarginInterface $labelMargin = null,
        ?ColorInterface $labelTextColor = null,
        // Logo options
        ?string $logoPath = null,
        ?int $logoResizeToWidth = null,
        ?int $logoResizeToHeight = null,
        ?bool $logoPunchoutBackground = null,
    ): ResultInterface {
        if ($this->validateResult && !$this->writer instanceof ValidatingWriterInterface) {
            throw ValidationException::createForUnsupportedWriter(get_class($this->writer));
        }

        $writer = $writer ?? $this->writer;
        $writerOptions = $writerOptions ?? $this->writerOptions;
        $validateResult = $validateResult ?? $this->validateResult;

        $createLabel = $this->labelText || $labelText;
        $createLogo = $this->logoPath || $logoPath;

        $qrCode = new QrCode(
            data: $data ?? $this->data,
            encoding: $encoding ?? $this->encoding,
            errorCorrectionLevel: $errorCorrectionLevel ?? $this->errorCorrectionLevel,
            size: $size ?? $this->size,
            margin: $margin ?? $this->margin,
            roundBlockSizeMode: $roundBlockSizeMode ?? $this->roundBlockSizeMode,
            foregroundColor: $foregroundColor ?? $this->foregroundColor,
            backgroundColor: $backgroundColor ?? $this->backgroundColor
        );

        $logo = $createLogo ? new Logo(
            path: $logoPath ?? $this->logoPath,
            resizeToWidth: $logoResizeToWidth ?? $this->logoResizeToWidth,
            resizeToHeight: $logoResizeToHeight ?? $this->logoResizeToHeight,
            punchoutBackground: $logoPunchoutBackground ?? $this->logoPunchoutBackground
        ) : null;

        $label = $createLabel ? new Label(
            text: $labelText ?? $this->labelText,
            font: $labelFont ?? $this->labelFont,
            alignment: $labelAlignment ?? $this->labelAlignment,
            margin: $labelMargin ?? $this->labelMargin,
            textColor: $labelTextColor ?? $this->labelTextColor
        ) : null;

        $result = $writer->write($qrCode, $logo, $label, $writerOptions);

        if ($validateResult && $writer instanceof ValidatingWriterInterface) {
            $writer->validateResult($result, $qrCode->getData());
        }

        return $result;
    }
}
