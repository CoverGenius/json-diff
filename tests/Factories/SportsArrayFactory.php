<?php

declare(strict_types=1);

namespace Jet\Tests\Factories;

use Illuminate\Support\Arr;

class SportsArrayFactory
{
    /**
     * @var string[]
     */
    protected $sports = [
        'soccer',
        'swimming',
        'tennis',
        'rugby',
        'badminton',
        'golf',
    ];

    /**
     * @var int
     */
    protected $length = 2;

    public static function new(): SportsArrayFactory
    {
        $factory = new self();
        $factory->sports = Arr::shuffle($factory->sports);
        return $factory;
    }

    public function create(array $extra = []): array
    {
        return $extra + array_splice(
            $this->sports,
            0,
            $this->length
            );
    }

    public function withSports(array $sports): SportsArrayFactory
    {
        $clone = clone $this;

        $clone->sports = $sports;

        return $clone;
    }

    public function length(int $length = 2): SportsArrayFactory
    {
        $clone = clone $this;

        $clone->length = $length;

        return $clone;
    }
}