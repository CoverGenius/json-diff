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

    /**
     * @var SortKeyDifferencesByNumberOfChangesAction
     */
    private $sortKeyDifferencesByNumberOfChangesAction;

    /**
     * @var SelectMinimalOriginalDiffsAction
     */
    private $selectMinimalOriginalDiffsAction;

    public function __construct(
        CalculateAllDifferencesRespectiveToOriginalAction $calculateAllDifferencesRespectiveToOriginalAction,
        SortKeyDifferencesByNumberOfChangesAction $sortKeyDifferencesByNumberOfChangesAction,
        SelectMinimalOriginalDiffsAction $selectMinimalOriginalDiffsAction
    ) {
        $this->calculateAllDifferencesRespectiveToOriginalAction = $calculateAllDifferencesRespectiveToOriginalAction;
        $this->sortKeyDifferencesByNumberOfChangesAction = $sortKeyDifferencesByNumberOfChangesAction;
        $this->selectMinimalOriginalDiffsAction = $selectMinimalOriginalDiffsAction;
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

        $diffMappings = $this
            ->sortKeyDifferencesByNumberOfChangesAction
            ->execute($diffMappings);

        $diffMappings = $this
            ->selectMinimalOriginalDiffsAction
            ->execute($diffMappings);

        dd($diffMappings);

        // Check if there were any additions or removals.
        if (count($original) !== count($new)) {
            for ($i = 0; $i < count($new); $i++) {
//                $selectedMinimalOriginalDiffMappings->has();
            }
        }
    }
}