<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class GetSortedOriginalDiffMappingsAction
{
    /**
     * @param Collection<DiffMapping> $diffMappings
     * @param array $original
     * @return Collection<DiffMapping>
     */
    public function execute(Collection $diffMappings, array $original): Collection
    {
        $sortedOriginalDiffMappings = collect();

        // Group by original key and sort the key's diffs by number of changes in ascending order
        for ($i = 0; $i < count($original); $i++) {
            $sortedOriginalDiffMappings
                ->offsetSet(
                    "{$i}",
                    $diffMappings
                        ->filter(function (DiffMapping $diffMapping) use ($i) {
                            return $diffMapping->getOriginalIndex() === $i;
                        })
                        ->sort(function (DiffMapping $diffMappingA, DiffMapping $diffMappingB): int {
                            return $diffMappingB->getDiff()->getNumberOfChanges() - $diffMappingA->getDiff()->getNumberOfChanges();
                        })
                );
        }

        // Sort the original keys by the least number of changes in ascending order
        return $sortedOriginalDiffMappings->sort(function (Collection $diffsA, Collection $diffsB) {
            return $diffsB->first()->getDiff()->getNumberOfChanges() - $diffsA->first()->getDiff()->getNumberOfChanges();
        });
    }
}