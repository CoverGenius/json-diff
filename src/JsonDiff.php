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

    public function __construct(array $original, array $new)
    {
        $this->keysAdded = collect();
        $this->keysRemoved = collect();
        $this->valuesAdded = collect();
        $this->valuesRemoved = collect();
        $this->valuesChanged = collect();

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
                dump($this->valuesChanged);
            }
        });
    }
}

