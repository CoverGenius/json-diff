<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;
use Jet\JsonDiff\JsonDiff;

class CalculateMinimalDiffOfListArrayAction
{
    /**
     * @var CalculateAllDifferencesRespectiveToOriginalAction
     */
    private $calculateAllDifferencesRespectiveToOriginalAction;

    public function __construct(
        CalculateAllDifferencesRespectiveToOriginalAction $calculateAllDifferencesRespectiveToOriginalAction
    ) {
        $this->calculateAllDifferencesRespectiveToOriginalAction = $calculateAllDifferencesRespectiveToOriginalAction;
    }

    public function execute(array $original, array $new, string $path): JsonDiff
    {
        $diffMappings = $this
            ->calculateAllDifferencesRespectiveToOriginalAction
            ->execute(
                $original,
                $new,
                $path
            );

//        $sortedOriginalDiffMappings =

        // Group by original key and sort the key's diffs by number of changes in ascending order
        $sortedOriginalDiffMappings = collect();
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
        $sortedOriginalDiffMappings = $sortedOriginalDiffMappings->sort(function (Collection $diffsA, Collection $diffsB) {
            return $diffsB->first()->getDiff()->getNumberOfChanges() - $diffsA->first()->getDiff()->getNumberOfChanges();
        });

        $keysProcessedInNewArray = [];
        $selectedMinimalOriginalDiffMappings = $sortedOriginalDiffMappings
            ->mapWithKeys(function (Collection $diffMappings, int $key) use (&$keysProcessedInNewArray) {
                $returnMapping = [
                    "{$key}" => $diffMappings->first(function (DiffMapping $diffMapping) use ($keysProcessedInNewArray) {
                        return ! in_array($diffMapping->getNewIndex(), $keysProcessedInNewArray);
                    })
                ];

                $keysProcessedInNewArray[] = $returnMapping["{$key}"]->getNewIndex();

                return $returnMapping;
            });

        // Check if there was a key in the new array that was not mapped
        if (count($original) !== count($new)) {
            for ($i = 0; $i < count($new); $i++) {
                $selectedMinimalOriginalDiffMappings->has();
            }
        }
    }
}