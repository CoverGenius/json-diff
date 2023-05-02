<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;
use Jet\JsonDiff\JsonDiff;

class AddRemovalsToJsonDiffAction
{
    /**
     * @var GetItemPathAction
     */
    private $getItemPathAction;

    public function __construct(GetItemPathAction $getItemPathAction)
    {
        $this->getItemPathAction = $getItemPathAction;
    }

    public function execute(Collection $diffMappings, array $original, JsonDiff $jsonDiff, string $path): JsonDiff
    {
        $diffMappings
            ->filter(function (?DiffMapping $diffMapping) {
                return $diffMapping === null;
            })
            ->map(function (?DiffMapping $diffMapping, int $originalIndex) {
                return $originalIndex;
            })
            ->each(function (int $originalKey) use ($original, $path, $jsonDiff) {
                $jsonDiff->addRemovedKey($this->getItemPathAction->execute($path, $originalKey), $originalKey, $original[$originalKey]);
            });

        return $jsonDiff;
    }
}