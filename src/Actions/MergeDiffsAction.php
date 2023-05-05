<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\JsonDiff;

class MergeDiffsAction
{
    /**
     * @param Collection<JsonDiff> $diffs
     */
    public function execute(Collection $diffs): JsonDiff
    {
        /** @var JsonDiff $mergedDiff */
        $mergedDiff = $diffs->shift();
        $diffs
            ->each(function (JsonDiff $diff) use ($mergedDiff): void {
                $mergedDiff->mergeChanges($diff);
            });

        return $mergedDiff;
    }
}
