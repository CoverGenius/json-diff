<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;
use Jet\JsonDiff\JsonDiff;

class CalculateAllDifferencesRespectiveToOriginalAction
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
     * Calculate the differences between the original keys and all of the new keys
     * group by the original key.
     *
     * @return Collection<DiffMapping>
     */
    public function execute(array $original, array $new, string $path): Collection
    {
        $diffMappings = collect();

        foreach ($original as $originalIndex => $originalValue) {
            $diffs = collect();

            foreach ($new as $newIndex => $newValue) {
                $diffs->push(
                    new DiffMapping(
                        $originalIndex,
                        $newIndex,
                        new JsonDiff(
                            $originalValue,
                            $newValue,
                            $this->getItemPathAction->execute($path, $originalIndex)
                        )
                    )
                );
            }
            $diffMappings->offsetSet($originalIndex, $diffs);
        }

        return $diffMappings;
    }
}
