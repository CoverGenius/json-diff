<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

use Illuminate\Support\Collection;
use Jet\JsonDiff\DiffMapping;
use Jet\JsonDiff\JsonDiff;

class CalculateAllDifferencesRespectiveToOriginalAction
{
    /**
     * @var GetTraversingPathAction
     */
    private $getTraversingPathAction;

    public function __construct(GetTraversingPathAction $getTraversingPathAction)
    {
        $this->getTraversingPathAction = $getTraversingPathAction;
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
                            $this->getTraversingPathAction->execute($path, $newIndex)
                        )
                    )
                );
            }
            $diffMappings->offsetSet($originalIndex, $diffs);
        }

        return $diffMappings;
    }
}