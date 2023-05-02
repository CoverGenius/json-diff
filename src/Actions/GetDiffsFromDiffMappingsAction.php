<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class GetDiffsFromDiffMappingsAction
{
    /**
     * @param Collection<DiffMapping> $diffMappings
     * @return Collection
     */
    public function execute(Collection $diffMappings): Collection
    {
        $jsonDiffs = collect();
        $diffMappings
            ->each(function (?DiffMapping $diffMapping) use (&$jsonDiffs) {
                if ($diffMapping) {
                    $jsonDiffs->push($diffMapping->getDiff());
                }
            });

        return $jsonDiffs;
    }
}