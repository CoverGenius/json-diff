<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

class KeyRemoved
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
}
