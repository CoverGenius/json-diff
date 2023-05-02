<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Jet\JsonDiff\Actions\CalculateAllDifferencesRespectiveToOriginalAction;
use Jet\JsonDiff\Actions\CalculateMinimalDiffOfListArrayAction;
use Jet\JsonDiff\Actions\GetItemPathAction;
use Jet\JsonDiff\Actions\GetTraversingPathAction;

class JsonDiff
{
    /**
     * @var Collection
     */
    protected $keysAdded;

    /**
     * @var Collection
     */
    private $keysRemoved;

    /**
     * @var Collection
     */
    private $valuesAdded;

    /**
     * @var Collection
     */
    private $valuesRemoved;

    /**
     * @var Collection
     */
    private $valuesChanged;

    /**
     * @var JsonHash
     */
    private $jsonHash;

    /**
     * @var Container
     */
    private $serviceContainer;

    /**
     * @var GetTraversingPathAction
     */
    private $getTraversingPathAction;

    /**
     * @var GetItemPathAction
     */
    private $getItemPathAction;

    /**
     * @var CalculateMinimalDiffOfListArrayAction
     */
    private $calculateMinimalDiffOfListArrayAction;

    public function __construct(array $original, array $new, string $startingPath = '')
    {
        $this->keysAdded = collect();
        $this->keysRemoved = collect();
        $this->valuesAdded = collect();
        $this->valuesRemoved = collect();
        $this->valuesChanged = collect();

        $this->serviceContainer = Container::getInstance();

        $this->getTraversingPathAction = $this->serviceContainer
            ->make(GetTraversingPathAction::class);
        $this->getItemPathAction = $this->serviceContainer
            ->make(GetItemPathAction::class);
        $this->calculateMinimalDiffOfListArrayAction = $this->serviceContainer
            ->make(CalculateMinimalDiffOfListArrayAction::class);

        $this->process($original, $new, $startingPath);
    }

    protected function process(array $original, array $new, string $path = ''): void
    {
        $originalKeys = array_keys($original);
        $newKeys = array_keys($new);

        $keysAdded = array_diff($newKeys, $originalKeys);
        collect($keysAdded)->each(function ($key) use ($new, $path) {
             $this->keysAdded->push(new KeyAdded($this->getItemPathAction->execute($path, $key), $key));
             $this->valuesAdded->push(new ValueAdded($this->getItemPathAction->execute($path, $key), $new[$key]));
        });

        $keysRemoved = array_diff($originalKeys, $newKeys);
        collect($keysRemoved)->each(function ($key) use ($original, $path) {
            $this->keysRemoved->push(new KeyRemoved($this->getItemPathAction->execute($path, $key), $key));
            $this->valuesRemoved->push(new ValueRemoved($this->getItemPathAction->execute($path, $key), $original[$key]));
        });

        $mutualKeys = array_intersect($originalKeys, $newKeys);
        // Check if value has changed
        collect($mutualKeys)->each(function ($key) use ($new, $original, $path) {
            // @todo if the value is an object, decide what to do
            $currentOriginal = $original[$key];
            $currentNew = $new[$key];

            if (is_array($currentOriginal) && is_array($currentNew)) {
                if (array_is_list($currentOriginal) && array_is_list($currentNew)) {
                    $this->mergeChanges(
                        $this
                            ->calculateMinimalDiffOfListArrayAction
                            ->execute(
                                $currentOriginal,
                                $currentNew,
                                $this->getTraversingPathAction->execute($path, $key)
                            )
                    );
                    return;
                }

                $this->process($currentOriginal, $currentNew, $this->getTraversingPathAction->execute($path, $key));
                return;
            }

            if ($currentOriginal !== $currentNew) {
                $this->valuesChanged->push(new ValueChange($this->getItemPathAction->execute($path, $key), $currentOriginal, $currentNew));
            }
        });
    }

    public function getNumberOfChanges(): int
    {
        return
            $this->keysAdded->count() +
            $this->keysRemoved->count() +
            $this->valuesAdded->count() +
            $this->valuesRemoved->count() +
            $this->valuesChanged->count();
    }

    protected function hasNoChanges(): bool
    {
        return
            $this->keysAdded->isEmpty() &&
            $this->keysRemoved->isEmpty() &&
            $this->valuesAdded->isEmpty() &&
            $this->valuesRemoved->isEmpty() &&
            $this->valuesChanged->isEmpty();
    }

    protected function mergeChanges(JsonDiff $jsonDiff): void
    {
        $this->keysAdded = $this->keysAdded->merge($jsonDiff->getKeysAdded());
        $this->keysRemoved = $this->keysRemoved->merge($jsonDiff->getKeysRemoved());
        $this->valuesAdded = $this->valuesAdded->merge($jsonDiff->getValuesAdded());
        $this->valuesRemoved = $this->valuesRemoved->merge($jsonDiff->getValuesRemoved());
        $this->valuesChanged = $this->valuesChanged->merge($jsonDiff->getValuesChanged());
    }

    protected function rearrangeArray(array $original, array $new): array
    {
        if ($this->jsonHash === null) {
            $this->jsonHash = new JsonHash();
        }

        // Rearrange nested arrays
        foreach ($original as $i => $item) {
            if (is_array($item) && is_array($new[$i])) {
                $new[$i] = $this->rearrangeArray($item, $new[$i]);
            }
        }

        $origIdx = [];
        foreach ($original as $i => $item) {
            $hash = $this->jsonHash->xorHash($item);
            $origIdx[$hash][] = $i;
        }

        $newIdx = [];
        foreach ($new as $i => $item) {
            $hash = $this->jsonHash->xorHash($item);
            $newIdx[$i] = $hash;
        }

        $newRearranged = [];
        $changedItems = [];
        foreach ($newIdx as $i => $hash) {
            if (!empty($origIdx[$hash])) {
                $j = array_shift($origIdx[$hash]);

                $newRearranged[$j] = $new[$i];
            } else {
                $changedItems[]= $new[$i];
            }

        }

        $idx = 0;
        foreach ($changedItems as $item) {
            while (array_key_exists($idx, $newRearranged)) {
                $idx++;
            }
            $newRearranged[$idx] = $item;
        }

        ksort($newRearranged);

        return $newRearranged;
    }

    public function getKeysAdded(): Collection
    {
        return $this->keysAdded;
    }

    public function getKeysRemoved(): Collection
    {
        return $this->keysRemoved;
    }

    public function getValuesAdded(): Collection
    {
        return $this->valuesAdded;
    }

    public function getValuesRemoved(): Collection
    {
        return $this->valuesRemoved;
    }

    public function getValuesChanged(): Collection
    {
        return $this->valuesChanged;
    }
}

