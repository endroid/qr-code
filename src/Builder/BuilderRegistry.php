<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

final class BuilderRegistry implements BuilderRegistryInterface
{
    /** @var array<BuilderInterface> */
    private array $builders = [];

    #[\Override]
    public function set(string $name, BuilderInterface $builder): void
    {
        $this->builders[$name] = $builder;
    }

    #[\Override]
    public function get(string $name): BuilderInterface
    {
        if (null === ($this->builders[$name] ?? null)) {
            throw new \Exception(sprintf('Builder with name "%s" not available from registry', $name));
        }

        return $this->builders[$name];
    }
}
