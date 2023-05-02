<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

class DiffMapping
{
    /**
     * @var int
     */
    protected $originalIndex;

    /**
     * @var int
     */
    protected $newIndex;

    /**
     * @var JsonDiff
     */
    protected $diff;

    public function __construct(int $originalIndex, int $newIndex, JsonDiff $diff)
    {
        $this->originalIndex = $originalIndex;
        $this->newIndex = $newIndex;
        $this->diff = $diff;
    }

    public function getOriginalIndex(): int
    {
        return $this->originalIndex;
    }

    public function getNewIndex(): int
    {
        return $this->newIndex;
    }

    public function getDiff(): JsonDiff
    {
        return $this->diff;
    }
}