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

    public function execute(array $original, array $new, string $path): Collection
    {
        $diffMappings = collect();

        // Calculate the differences between the original keys and all of the new keys
        foreach ($original as $originalIndex => $originalValue) {
            foreach ($new as $newIndex => $newValue) {
                $diffMappings->push(
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
        }

        return $diffMappings;
    }
}