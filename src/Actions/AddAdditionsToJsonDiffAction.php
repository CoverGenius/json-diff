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

    public function __construct(GetItemPathAction $getItemPathAction)
    {
        $this->getItemPathAction = $getItemPathAction;
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
        collect(array_keys($new))
            ->diff(
                $diffMappings
                    ->map(function (?DiffMapping $diffMapping) {
                        return optional($diffMapping)->getNewIndex();
                    })
                    ->filter(function (?int $index) {
                        return $index !== null;
                    })
            )
            ->each(function (int $newKey) use ($path, $new, $jsonDiff) {
                $jsonDiff->addAddedKey($this->getItemPathAction->execute($path, $newKey), $newKey, $new[$newKey]);
            });

        return $jsonDiff;
    }
}