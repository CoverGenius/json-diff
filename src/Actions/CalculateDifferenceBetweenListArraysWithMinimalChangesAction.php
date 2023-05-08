<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Jet\JsonDiff\JsonDiff;

class CalculateDifferenceBetweenListArraysWithMinimalChangesAction
{
    /**
     * @var CalculateAllDifferencesRespectiveToOriginalAction
     */
    private $calculateAllDifferencesRespectiveToOriginalAction;

    /**
     * @var SortDifferencesByNumberOfChangesAction
     */
    private $sortDifferencesByNumberOfChangesAction;

    /**
     * @var SelectJsonDiffsForOriginalKeysWithMinimalChangesAction
     */
    private $selectJsonDiffsForOriginalKeysWithMinimalChangesAction;

    /**
     * @var GetItemPathAction
     */
    private $getItemPathAction;

    /**
     * @var MergeJsonDiffsAction
     */
    private $mergeJsonDiffsAction;

    /**
     * @var GetJsonDiffsFromDiffMappingsAction
     */
    private $getJsonDiffsFromDiffMappingsAction;

    /**
     * @var AddAdditionsToJsonDiffAction
     */
    private $addAdditionsToJsonDiffAction;

    /**
     * @var AddRemovalsToJsonDiffAction
     */
    private $addRemovalsToJsonDiffAction;

    public function __construct(
        CalculateAllDifferencesRespectiveToOriginalAction $calculateAllDifferencesRespectiveToOriginalAction,
        SortDifferencesByNumberOfChangesAction $sortDifferencesByNumberOfChangesAction,
        SelectJsonDiffsForOriginalKeysWithMinimalChangesAction $selectJsonDiffsForOriginalKeysWithMinimalChangesAction,
        GetItemPathAction $getItemPathAction,
        MergeJsonDiffsAction $mergeJsonDiffsAction,
        GetJsonDiffsFromDiffMappingsAction $getJsonDiffsFromDiffMappingsAction,
        AddAdditionsToJsonDiffAction $addAdditionsToJsonDiffAction,
        AddRemovalsToJsonDiffAction $addRemovalsToJsonDiffAction
    ) {
        $this->calculateAllDifferencesRespectiveToOriginalAction = $calculateAllDifferencesRespectiveToOriginalAction;
        $this->sortDifferencesByNumberOfChangesAction = $sortDifferencesByNumberOfChangesAction;
        $this->selectJsonDiffsForOriginalKeysWithMinimalChangesAction = $selectJsonDiffsForOriginalKeysWithMinimalChangesAction;
        $this->getItemPathAction = $getItemPathAction;
        $this->mergeJsonDiffsAction = $mergeJsonDiffsAction;
        $this->getJsonDiffsFromDiffMappingsAction = $getJsonDiffsFromDiffMappingsAction;
        $this->addAdditionsToJsonDiffAction = $addAdditionsToJsonDiffAction;
        $this->addRemovalsToJsonDiffAction = $addRemovalsToJsonDiffAction;
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
            ->sortDifferencesByNumberOfChangesAction
            ->execute($diffMappings);

        $diffMappings = $this
            ->selectJsonDiffsForOriginalKeysWithMinimalChangesAction
            ->execute($diffMappings);

        $jsonDiff = $this
            ->mergeJsonDiffsAction
            ->execute(
                $this
                    ->getJsonDiffsFromDiffMappingsAction
                    ->execute($diffMappings)
            );

        // Check if there were any additions
        $jsonDiff = $this
            ->addAdditionsToJsonDiffAction
            ->execute($diffMappings, $new, $jsonDiff, $path);

        // Check if there were any removals
        return $this
            ->addRemovalsToJsonDiffAction
            ->execute($diffMappings, $original, $jsonDiff, $path);
    }
}
