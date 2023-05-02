<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class SortKeyDifferencesByNumberOfChangesAction
{
    public function execute(Collection $diffMappings): Collection
    {
        return $diffMappings
            ->map(function (Collection $diffMappings) {
                return $diffMappings
                    ->sort(function (DiffMapping $diffMappingA, DiffMapping $diffMappingB): int {
                        return $diffMappingA->getDiff()->getNumberOfChanges() - $diffMappingB->getDiff()->getNumberOfChanges();
                    })
                    ->values();
            });
    }
}