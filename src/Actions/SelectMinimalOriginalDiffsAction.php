<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class SelectMinimalOriginalDiffsAction
{
    /**
     * @param Collection<Collection<DiffMapping>> $diffs
     * @return Collection<DiffMapping>
     */
    public function execute(Collection $diffs): Collection
    {
        $keysProcessedInNewArray = collect();

        do {
            $addedMapping = false;

            $diffs->each(function (Collection $diffMappings) use (&$keysProcessedInNewArray, &$addedMapping) {
                $diffMappings->each(function (DiffMapping $diffMapping) use (&$keysProcessedInNewArray, &$addedMapping) {
                    // If we find a diff mapping with less changes than an existing mapping, replace it
                    if (
                        $keysProcessedInNewArray->has($diffMapping->getNewIndex()) &&
                        $diffMapping
                            ->getDiff()
                            ->getNumberOfChanges() >=
                        $keysProcessedInNewArray
                            ->get($diffMapping->getNewIndex())
                            ->getDiff()
                            ->getNumberOfChanges()
                    ) {
                        return;
                    }

                    // Add the mapping
                    $keysProcessedInNewArray
                        ->offsetSet(
                            $diffMapping->getNewIndex(),
                            $diffMapping
                        );
                    $addedMapping = true;
                });
            });
        } while ($addedMapping);

        // For each original index, return the found diff mapping
        return $keysProcessedInNewArray->mapWithKeys(function (DiffMapping $diffMapping) {
            return [
                $diffMapping->getOriginalIndex() => $diffMapping,
            ];
        });
    }
}