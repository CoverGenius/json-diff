<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\JsonDiff;

class MergeJsonDiffsAction
{
    /**
     * @param Collection<JsonDiff> $jsonDiffs
     */
    public function execute(Collection $jsonDiffs): JsonDiff
    {
        $mergedJsonDiff = new JsonDiff([], []);
        $jsonDiffs
            ->each(function (JsonDiff $jsonDiff) use ($mergedJsonDiff): void {
                $mergedJsonDiff->mergeChanges($jsonDiff);
            });

        return $mergedJsonDiff;
    }
}
