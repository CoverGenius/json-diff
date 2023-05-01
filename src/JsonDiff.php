<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

use Exception;
use Illuminate\Support\Collection;

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

    public function __construct(array $original, array $new)
    {
        $this->keysAdded = collect();
        $this->keysRemoved = collect();
        $this->valuesAdded = collect();
        $this->valuesRemoved = collect();
        $this->valuesChanged = collect();

        $new = $this->rearrangeArray($original, $new);
        $this->process($original, $new);
        dd($this);
    }

    protected function process(array $original, array $new, string $path = ''): void
    {
        $originalKeys = array_keys($original);
        $newKeys = array_keys($new);

        $keysAdded = array_diff($newKeys, $originalKeys);
        collect($keysAdded)->each(function ($key) use ($new, $path) {
             $this->keysAdded->push(new KeyAdded("{$path}{$key}", $key));
             $this->valuesAdded->push(new ValueAdded("{$path}{$key}", $new[$key]));
        });

        $keysRemoved = array_diff($originalKeys, $newKeys);
        collect($keysRemoved)->each(function ($key) use ($original, $path) {
            $this->keysRemoved->push(new KeyRemoved("{$path}{$key}", $key));
            $this->valuesRemoved->push(new ValueRemoved("{$path}{$key}", $original[$key]));
        });

        $mutualKeys = array_intersect($originalKeys, $newKeys);
        // Check if value has changed
        collect($mutualKeys)->each(function ($key) use ($new, $original, $path) {
            // @todo if the value is an object, decide what to do
            if (is_array($original[$key]) && is_array($new[$key])) {
                $this->process($original[$key], $new[$key], "{$path}{$key}.");
                return;
            }

            if ($original[$key] !== $new[$key]) {
                $this->valuesChanged->push(new ValueChange("{$path}{$key}", $original[$key], $new[$key]));
            }
        });
    }

    /**
     * @param array $original
     * @param array $new
     * @return array
     */
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
}

