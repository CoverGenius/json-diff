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
    private $sortKeyDifferencesByNumberOfChangesAction;

    /**
     * @var SelectMinimalOriginalDiffsAction
     */
    private $selectMinimalOriginalDiffsAction;

    /**
     * @var GetItemPathAction
     */
    private $getItemPathAction;

    /**
     * @var MergeJsonDiffsAction
     */
    private $mergeDiffsAction;

    /**
     * @var GetJsonDiffsFromDiffMappingsAction
     */
    private $getDiffsFromDiffMappingsAction;

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
        SortDifferencesByNumberOfChangesAction            $sortKeyDifferencesByNumberOfChangesAction,
        SelectMinimalOriginalDiffsAction                  $selectMinimalOriginalDiffsAction,
        GetItemPathAction                                 $getItemPathAction,
        MergeJsonDiffsAction                              $mergeDiffsAction,
        GetJsonDiffsFromDiffMappingsAction                $getDiffsFromDiffMappingsAction,
        AddAdditionsToJsonDiffAction                      $addAdditionsToJsonDiffAction,
        AddRemovalsToJsonDiffAction                       $addRemovalsToJsonDiffAction
    ) {
        $this->calculateAllDifferencesRespectiveToOriginalAction = $calculateAllDifferencesRespectiveToOriginalAction;
        $this->sortKeyDifferencesByNumberOfChangesAction = $sortKeyDifferencesByNumberOfChangesAction;
        $this->selectMinimalOriginalDiffsAction = $selectMinimalOriginalDiffsAction;
        $this->getItemPathAction = $getItemPathAction;
        $this->mergeDiffsAction = $mergeDiffsAction;
        $this->getDiffsFromDiffMappingsAction = $getDiffsFromDiffMappingsAction;
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
            ->sortKeyDifferencesByNumberOfChangesAction
            ->execute($diffMappings);

        $diffMappings = $this
            ->selectMinimalOriginalDiffsAction
            ->execute($diffMappings);

        $jsonDiff = $this
            ->mergeDiffsAction
            ->execute(
                $this
                    ->getDiffsFromDiffMappingsAction
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
