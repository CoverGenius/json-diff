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

    /**
     * @var GetItemPathAction
     */
    private $getItemPathAction;

    /**
     * @var MergeDiffsAction
     */
    private $mergeDiffsAction;

    /**
     * @var GetDiffsFromDiffMappingsAction
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
        SortKeyDifferencesByNumberOfChangesAction $sortKeyDifferencesByNumberOfChangesAction,
        SelectMinimalOriginalDiffsAction $selectMinimalOriginalDiffsAction,
        GetItemPathAction $getItemPathAction,
        MergeDiffsAction $mergeDiffsAction,
        GetDiffsFromDiffMappingsAction $getDiffsFromDiffMappingsAction,
        AddAdditionsToJsonDiffAction $addAdditionsToJsonDiffAction,
        AddRemovalsToJsonDiffAction $addRemovalsToJsonDiffAction
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