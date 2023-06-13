<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class SelectJsonDiffsForOriginalKeysWithMinimalChangesAction
{
    /**
     * @param Collection<Collection<DiffMapping>> $diffs
     * @return Collection<DiffMapping>
     */
    public function execute(Collection $diffs): Collection
    {
        $keysProcessedInNewArray = collect();
        $originalToNewIndexMapping = collect();

        do {
            $modifiedMapping = false;

            $diffs->each(function (Collection $diffMappings) use (&$originalToNewIndexMapping, &$keysProcessedInNewArray, &$modifiedMapping): void {
                $diffMappings->each(function (DiffMapping $diffMapping) use (&$originalToNewIndexMapping, &$keysProcessedInNewArray, &$modifiedMapping): void {
                    /*
                     * If this diff mapping's new index has already been mapped, and it has fewer changes
                     * skip this diff mapping.
                     */
                    if (
                        $keysProcessedInNewArray->has($diffMapping->getNewIndex())
                        && $diffMapping
                            ->getDiff()
                            ->getNumberOfChanges()
                        >= $keysProcessedInNewArray
                            ->get($diffMapping->getNewIndex())
                            ->getDiff()
                            ->getNumberOfChanges()
                    ) {
                        return;
                    }

                    /*
                     * If this diff mapping's original index has already been mapped, and it has fewer changes
                     * skip this diff mapping.
                     */
                    if (
                        $originalToNewIndexMapping->has($diffMapping->getOriginalIndex())
                        && $diffMapping
                            ->getDiff()
                            ->getNumberOfChanges()
                        >= $keysProcessedInNewArray
                            ->get($originalToNewIndexMapping->get($diffMapping->getOriginalIndex()))
                            ->getDiff()
                            ->getNumberOfChanges()
                    ) {
                        return;
                    }

                    // Changes will happen so we need to keep looping
                    $modifiedMapping = true;

                    // Add the mapping
                    $keysProcessedInNewArray
                        ->offsetSet(
                            $diffMapping->getNewIndex(),
                            $diffMapping
                        );

                    /*
                     * Add an original index to new index mapping to track if an original index has been mapped to a new
                     * index already
                     */
                    $originalToNewIndexMapping
                        ->offsetSet(
                            $diffMapping->getOriginalIndex(),
                            $diffMapping->getNewIndex()
                        );

                    // Unmap any other original indexes that may have been mapped to this diff mapping's new index
                    $originalToNewIndexMapping
                        ->filter(function ($newIndex, $originalIndex) use ($diffMapping) {
                            return $originalIndex !== $diffMapping->getOriginalIndex() && $newIndex === $diffMapping->getNewIndex();
                        })
                        ->keys()
                        ->each(function ($originalIndex) use (&$originalToNewIndexMapping): void {
                            $originalToNewIndexMapping->offsetUnset($originalIndex);
                        });
                });
            });
        } while ($modifiedMapping);

        // For each original index, return the found diff mapping
        return $keysProcessedInNewArray->mapWithKeys(function (DiffMapping $diffMapping) {
            return [
                $diffMapping->getOriginalIndex() => $diffMapping,
            ];
        });
    }
}
