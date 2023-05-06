<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\JsonDiff;

class AddRemovalsToJsonDiffAction
{
    /**
     * @var GetItemPathAction
     */
    private $getItemPathAction;

    /**
     * @var GetUnmappedOriginalIndexesAction
     */
    private $getUnmappedOriginalIndexesAction;

    public function __construct(
        GetItemPathAction $getItemPathAction,
        GetUnmappedOriginalIndexesAction $getUnmappedOriginalIndexesAction
    ) {
        $this->getItemPathAction = $getItemPathAction;
        $this->getUnmappedOriginalIndexesAction = $getUnmappedOriginalIndexesAction;
    }

    public function execute(Collection $diffMappings, array $original, JsonDiff $jsonDiff, string $path): JsonDiff
    {
        $this
            ->getUnmappedOriginalIndexesAction
            ->execute($diffMappings, $original)
            ->each(function (int $originalKey) use ($original, $path, $jsonDiff): void {
                $jsonDiff->addRemovedKey($this->getItemPathAction->execute($path, $originalKey), $originalKey, $original[$originalKey]);
            });

        return $jsonDiff;
    }
}
