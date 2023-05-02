<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;

class SelectMinimalOriginalDiffsAction
{
    public function execute(Collection $diffs): Collection
    {
        $keysProcessedInNewArray = [];

        return
            $diffs->mapWithKeys(function (Collection $diffMappings, int $key) use (&$keysProcessedInNewArray) {
                $returnMapping = [
                    "{$key}" => $diffMappings->first(function (DiffMapping $diffMapping) use (&$keysProcessedInNewArray) {
                        return ! in_array($diffMapping->getNewIndex(), $keysProcessedInNewArray);
                    })
                ];

                $keysProcessedInNewArray[] = $returnMapping["{$key}"]->getNewIndex();

                return $returnMapping;
            });
    }
}