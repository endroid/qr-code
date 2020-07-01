<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Exception\ProfileNotFoundException;

class BuilderFactory implements BuilderFactoryInterface
{
    private $profiles = [];

    public function __construct(array $profiles)
    {
        $this->profiles = $profiles;
    }

    public function create(string $name): BuilderInterface
    {
        if (!isset($this->profiles[$name])) {
            throw ProfileNotFoundException::create($name);
        }

        return Builder::create()
            ->withWriter($this->profiles[$name]['writer'])
            ->withOptions($this->profiles[$name]['options'])
        ;
    }
}
