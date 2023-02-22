<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

use Endroid\QrCode\Matrix\MatrixInterface;

abstract class AbstractGdResult extends AbstractResult
{
    public function __construct(
        MatrixInterface $matrix,
        protected \GdImage $image,
        protected array $options = []
    ) {
        parent::__construct($matrix);

        $this->initOptions();
    }

    protected function initOptions(): void
    {
    }

    public function getImage(): \GdImage
    {
        return $this->image;
    }
}
