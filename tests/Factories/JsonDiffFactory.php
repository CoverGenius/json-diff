<?php

declare(strict_types=1);

namespace Jet\Tests\Factories;

use Illuminate\Support\Collection;
use Jet\JsonDiff\JsonDiff;

class JsonDiffFactory
{
    /**
     * @var bool
     */
    protected $createListArray = false;

    /**
     * @var array
     */
    protected $original;

    /**
     * @var array
     */
    protected $new;

    public static function new(): self
    {
        return new self();
    }

    public function create(?array $original = null, ?array $new = null): JsonDiff
    {
        if ($this->createListArray) {
            $arrayFactory = ListArrayFactory::new();
        } else {
            $arrayFactory = AssociativeArrayFactory::new();
        }

        return new JsonDiff(
            $original ?? $this->original ?? $arrayFactory->create(),
            $new ?? $this->new ?? $arrayFactory->create()
        );
    }

    /**
     * @return Collection<JsonDiff>
     */
    public function createMany(int $count): Collection
    {
        $jsonDiffs = collect([]);

        for ($i = 0; $i < $count; $i++) {
            $jsonDiffs->push($this->create());
        }

        return $jsonDiffs;
    }

    public function withOriginal(array $original): self
    {
        $clone = clone $this;
        $clone->original = $original;

        return $clone;
    }

    public function withNew(array $new): self
    {
        $clone = clone $this;
        $clone->new = $new;

        return $clone;
    }

    public function forAssociativeArray(): self
    {
        $clone = clone $this;
        $this->createListArray = false;

        return $clone;
    }

    public function forListArray(): self
    {
        $clone = clone $this;
        $this->createListArray = true;

        return $clone;
    }
}
