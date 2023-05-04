<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

class ValueRemoved
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

    public function getPath(): string
    {
        return $this->path;
    }

    public function getValue()
    {
        return $this->value;
    }
}
