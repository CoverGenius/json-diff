<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

class ValueAdded
{
    /**
     * @var string
     */
    private $path;
    private $value;

    public function __construct(string $path, $value)
    {
        $this->path = $path;
        $this->value = $value;
    }
}
