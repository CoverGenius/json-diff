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
        $originalToNewIndexMapping = [];

        do {
            $addedMapping = false;

            $diffs->each(function (Collection $diffMappings) use (&$originalToNewIndexMapping, &$keysProcessedInNewArray, &$addedMapping) {
                $diffMappings->each(function (DiffMapping $diffMapping) use (&$originalToNewIndexMapping, &$keysProcessedInNewArray, &$addedMapping) {
                    // If we find a diff mapping with lesser changes than an existing mapping, replace it
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

                    // If we have an existing diff mapping to the original index with lesser changes than the current
                    // diff mapping, skip replacing the mapping with the current index
                    if (
                        array_key_exists($diffMapping->getOriginalIndex(), $originalToNewIndexMapping) &&
                        $diffMapping
                            ->getDiff()
                            ->getNumberOfChanges() >=
                        $keysProcessedInNewArray
                            ->get($originalToNewIndexMapping[$diffMapping->getOriginalIndex()])
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

                    // Add an original index to new index mapping to track if an original index has been mapped to a new
                    // index already
                    $originalToNewIndexMapping[$diffMapping->getOriginalIndex()] = $diffMapping->getNewIndex();

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
