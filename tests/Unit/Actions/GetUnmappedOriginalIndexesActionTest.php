<?php

declare(strict_types=1);

namespace Jet\Tests\Unit\Actions;

use Illuminate\Container\Container;
use Jet\JsonDiff\Actions\GetUnmappedOriginalIndexesAction;
use Jet\JsonDiff\DiffMapping;
use Jet\JsonDiff\JsonDiff;
use Jet\Tests\TestCase;

class GetUnmappedOriginalIndexesActionTest extends TestCase
{
    public function test_it_returns_the_indexes_in_the_original_array_that_were_not_mapped(): void
    {
        // Original array has 3 items
        $originalArray = ['a', 'b', 'c'];

        // Only the first item found a mapping in the "new" array
        $diffMappings = collect([
            new DiffMapping(0, 0, $this->createMock(JsonDiff::class)),
        ]);

        /** @var GetUnmappedOriginalIndexesAction $action */
        $action = Container::getInstance()->make(GetUnmappedOriginalIndexesAction::class);
        $unmappedIndexes = $action->execute($diffMappings, $originalArray);

        // There should be 2 unmapped items at index 1 and 2 from the original array
        $this->assertCount(2, $unmappedIndexes);
        $this->assertSame(1, $unmappedIndexes[0]);
        $this->assertSame(2, $unmappedIndexes[1]);
    }
}
