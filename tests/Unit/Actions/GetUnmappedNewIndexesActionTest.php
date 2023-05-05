<?php

declare(strict_types=1);

namespace Jet\Tests\Unit\Actions;

use Illuminate\Container\Container;
use Jet\JsonDiff\Actions\GetUnmappedNewIndexesAction;
use Jet\JsonDiff\DiffMapping;
use Jet\JsonDiff\JsonDiff;
use Jet\Tests\TestCase;

class GetUnmappedNewIndexesActionTest extends TestCase
{
    public function test_it_returns_the_indexes_in_the_new_array_that_were_not_mapped(): void
    {
        // New array has 3 items
        $newArray = ['a', 'b', 'c'];

        // Only the first item found a mapping from the "original" array
        $diffMappings = collect([
            new DiffMapping(0, 0, $this->createMock(JsonDiff::class)),
        ]);

        /** @var GetUnmappedNewIndexesAction $action */
        $action = Container::getInstance()->make(GetUnmappedNewIndexesAction::class);
        $unmappedIndexes = $action->execute($diffMappings, $newArray);

        // There should be 2 unmapped items at index 1 and 2 of the new array
        $this->assertCount(2, $unmappedIndexes);
        $this->assertSame(1, $unmappedIndexes[0]);
        $this->assertSame(2, $unmappedIndexes[1]);
    }
}
