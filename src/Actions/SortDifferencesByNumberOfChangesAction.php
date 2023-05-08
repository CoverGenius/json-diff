<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class SortDifferencesByNumberOfChangesAction
{
    public function execute(Collection $diffMappings): Collection
    {
        return $diffMappings
            ->map(function (Collection $diffMappings) {
                return $diffMappings
                    ->sort(function (DiffMapping $diffMappingA, DiffMapping $diffMappingB): int {
                        $sortResult = $diffMappingA->getDiff()->getNumberOfChanges() - $diffMappingB->getDiff()->getNumberOfChanges();

                        // If the number of changes are the same, prioritise value changes over key additions
                        if ($sortResult === 0) {
                            $sortResult = $diffMappingA->getDiff()->getValuesChanged()->count() - $diffMappingB->getDiff()->getValuesChanged()->count();
                        }

                        return $sortResult;
                    })
                    ->values();
            });
    }
}
