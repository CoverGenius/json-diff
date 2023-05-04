<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

class KeyAdded
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string|int
     */
    private $name;

    public function __construct(string $path, $name)
    {
        $this->path = $path;
        $this->name = $name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getKey()
    {
        return $this->name;
    }
}
