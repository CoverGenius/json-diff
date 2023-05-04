<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class GetUnmappedOriginalIndexesAction
{
    public function execute(Collection $diffMappings): Collection
    {
        return $diffMappings
            ->filter(function (?DiffMapping $diffMapping) {
                return $diffMapping === null;
            })
            ->map(function (?DiffMapping $diffMapping, int $originalIndex) {
                return $originalIndex;
            })
            ->values();
    }
}