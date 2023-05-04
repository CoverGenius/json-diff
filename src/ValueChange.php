<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

class ValueChange
{
    /**
     * @var string
     */
    private $path;

    private $oldValue;

    private $newValue;

    public function __construct(string $path, $oldValue, $newValue)
    {
        $this->path = $path;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getOldValue()
    {
        return $this->oldValue;
    }

    public function getNewValue()
    {
        return $this->newValue;
    }
}
