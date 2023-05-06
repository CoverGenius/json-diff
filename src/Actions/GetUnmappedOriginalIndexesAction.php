<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class GetUnmappedOriginalIndexesAction
{
    /**
     * @param Collection<DiffMapping> $diffMappings
     * @return Collection<int>
     */
    public function execute(Collection $diffMappings, array $original): Collection
    {
        return collect(array_keys($original))
            ->diff(
                $diffMappings
                    ->map(function (DiffMapping $diffMapping) {
                        return $diffMapping->getOriginalIndex();
                    })
            )
            ->values();
    }
}
