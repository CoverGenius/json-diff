<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Jet\JsonDiff\Actions\CalculateMinimalDiffOfListArrayAction;
use Jet\JsonDiff\Actions\GetItemPathAction;
use function is_array;

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
     * @var Container
     */
    private $serviceContainer;

    /**
     * @var GetItemPathAction
     */
    private $getItemPathAction;

    /**
     * @var CalculateMinimalDiffOfListArrayAction
     */
    private $calculateMinimalDiffOfListArrayAction;

    /**
     * @param array|bool|float|int|string|null $original
     * @param array|bool|float|int|string|null $new
     */
    public function __construct($original, $new, string $startingPath = '')
    {
        $this->keysAdded = collect();
        $this->keysRemoved = collect();
        $this->valuesAdded = collect();
        $this->valuesRemoved = collect();
        $this->valuesChanged = collect();

        $this->serviceContainer = Container::getInstance();

        $this->getItemPathAction = $this->serviceContainer
            ->make(GetItemPathAction::class);
        $this->calculateMinimalDiffOfListArrayAction = $this->serviceContainer
            ->make(CalculateMinimalDiffOfListArrayAction::class);

        // No differences
        if ($original === $new) {
            return;
        }

        // If either is not an array, just a value change occurred
        if (! is_array($original) || ! is_array($new)) {
            $this->valuesChanged->push(
                new ValueChange(
                    $startingPath,
                    $original,
                    $new
                )
            );

            return;
        }

        if (array_is_list($original) && array_is_list($new)) {
            $this
                ->mergeChanges(
                    $this
                        ->calculateMinimalDiffOfListArrayAction
                        ->execute($original, $new, $startingPath)
                );
        } else {
            $this->process($original, $new, $startingPath);
        }
    }

    protected function process(array $original, array $new, string $path = ''): void
    {
        $originalKeys = array_keys($original);
        $newKeys = array_keys($new);

        $keysAdded = array_diff($newKeys, $originalKeys);
        collect($keysAdded)->each(function ($key) use ($new, $path): void {
            $this->addAddedKey($this->getItemPathAction->execute($path, $key), $key, $new[$key]);
        });

        $keysRemoved = array_diff($originalKeys, $newKeys);
        collect($keysRemoved)->each(function ($key) use ($original, $path): void {
            $this->addRemovedKey($this->getItemPathAction->execute($path, $key), $key, $original[$key]);
        });

        $mutualKeys = array_intersect($originalKeys, $newKeys);
        // Check if value has changed
        collect($mutualKeys)->each(function ($key) use ($new, $original, $path): void {
            /** @todo if the value is an object, decide what to do */
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
                                $this->getItemPathAction->execute($path, $key)
                            )
                    );

                    return;
                }

                $this->process($currentOriginal, $currentNew, $this->getItemPathAction->execute($path, $key));

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
            $this->keysAdded->count()
            + $this->keysRemoved->count()
            + $this->valuesChanged->count();
    }

    public function mergeChanges(self $jsonDiff): void
    {
        $this->keysAdded = $this->keysAdded->merge($jsonDiff->getKeysAdded());
        $this->keysRemoved = $this->keysRemoved->merge($jsonDiff->getKeysRemoved());
        $this->valuesAdded = $this->valuesAdded->merge($jsonDiff->getValuesAdded());
        $this->valuesRemoved = $this->valuesRemoved->merge($jsonDiff->getValuesRemoved());
        $this->valuesChanged = $this->valuesChanged->merge($jsonDiff->getValuesChanged());
    }

    /**
     * @param int|string $keyName
     * @param $value
     * @return $this
     */
    public function addAddedKey(string $path, $keyName, $value): self
    {
        $this->keysAdded->push(new KeyAdded($path, $keyName));
        $this->valuesAdded->push(new ValueAdded($path, $value));

        return $this;
    }

    /**
     * @param int|string $name
     * @param $value
     * @return $this
     */
    public function addRemovedKey(string $path, $name, $value): self
    {
        $this->keysRemoved->push(new KeyRemoved($path, $name));
        $this->valuesRemoved->push(new ValueRemoved($path, $value));

        return $this;
    }

    /**
     * @return Collection<KeyAdded>
     */
    public function getKeysAdded(): Collection
    {
        return $this->keysAdded;
    }

    /**
     * @return Collection<KeyRemoved>
     */
    public function getKeysRemoved(): Collection
    {
        return $this->keysRemoved;
    }

    /**
     * @return Collection<ValueAdded>
     */
    public function getValuesAdded(): Collection
    {
        return $this->valuesAdded;
    }

    /**
     * @return Collection<ValueRemoved>
     */
    public function getValuesRemoved(): Collection
    {
        return $this->valuesRemoved;
    }

    /**
     * @return Collection<ValueChange>
     */
    public function getValuesChanged(): Collection
    {
        return $this->valuesChanged;
    }
}
