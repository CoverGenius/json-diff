<?php

declare(strict_types=1);

namespace Jet\Tests\Factories;

class ListArrayFactory
{
    /**
     * @var int
     */
    protected $length = 1;

    public static function new(): ListArrayFactory
    {
        return new self();
    }

    public function create(array $extra = []): array
    {
        $array = [];

        for($i = 0; $i < $this->length - count($extra); $i++) {
            $array[] = AssociativeArrayFactory::new()->create();
        }

        if ($extra) {
            $array[] = $extra;
        }

        return $array;
    }

    public function length(int $length = 1): ListArrayFactory
    {
        $clone = clone $this;
        $clone->length = $length;
        return $clone;
    }
}