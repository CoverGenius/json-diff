<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class GetUnmappedNewIndexesAction
{
    /**
     * @param Collection<DiffMapping> $diffMappings
     * @param array $new
     * @return Collection<int>
     */
    public function execute(Collection $diffMappings, array $new): Collection
    {
        return collect(array_keys($new))
            ->diff(
                $diffMappings
                    ->map(function (DiffMapping $diffMapping) {
                        return $diffMapping->getNewIndex();
                    })
            )
            ->values();
    }
}