<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;
use Jet\JsonDiff\JsonDiff;

class AddAdditionsToJsonDiffAction
{
    /**
     * @var GetItemPathAction
     */
    private $getItemPathAction;

    /**
     * @var GetUnmappedNewIndexesAction
     */
    private $getUnmappedNewIndexesAction;

    public function __construct(
        GetItemPathAction $getItemPathAction,
        GetUnmappedNewIndexesAction $getUnmappedNewIndexesAction
    ) {
        $this->getItemPathAction = $getItemPathAction;
        $this->getUnmappedNewIndexesAction = $getUnmappedNewIndexesAction;
    }

    /**
     * @param Collection<DiffMapping> $diffMappings
     * @param array $new
     * @param JsonDiff $jsonDiff
     * @param string $path
     * @return JsonDiff
     */
    public function execute(Collection $diffMappings, array $new, JsonDiff $jsonDiff, string $path): JsonDiff
    {
        $this
            ->getUnmappedNewIndexesAction
            ->execute($diffMappings, $new)
            ->each(function (int $newKey) use ($path, $new, $jsonDiff) {
                $jsonDiff->addAddedKey($this->getItemPathAction->execute($path, $newKey), $newKey, $new[$newKey]);
            });

        return $jsonDiff;
    }
}