<?php

declare(strict_types=1);

namespace Jet\Tests\Factories;

use Illuminate\Support\Arr;

class AssociativeArrayFactory
{
    public static function new(): AssociativeArrayFactory
    {
        return new self();
    }

    public function create(array $extra = []): array
    {
        return $extra + [
            'name' => fake()->name,
            'age' => fake()->numberBetween(0, 100),
            /** @see https://en.wikipedia.org/wiki/List_of_tallest_people */
            'height' => fake()->randomFloat(2, 0, 272),
            'sports' => SportsArrayFactory::new()->create(),
            'is_active' => Arr::random([false, true]),
            'abn' => Arr::random([
                null,
                (string) fake()->numberBetween()
            ])
        ];
    }
}