<?php

declare(strict_types=1);

namespace Jet\Tests\Unit\Actions;

use Illuminate\Container\Container;
use Jet\JsonDiff\Actions\AddRemovalsToJsonDiffAction;
use Jet\JsonDiff\Actions\GetUnmappedOriginalIndexesAction;
use Jet\JsonDiff\JsonDiff;
use Jet\JsonDiff\ValueRemoved;
use Jet\Tests\TestCase;

class AddRemovalsToJsonDiffActionTest extends TestCase
{
    public function test_it_adds_removals_to_a_json_diff(): void
    {
        $originalArray = [
            0 => 'mapped',
            1 => 'unmapped',
        ];

        $this
            ->stub(GetUnmappedOriginalIndexesAction::class)
            ->method('execute')
            ->willReturn(
                collect([1])
            );

        $jsonDiff = new JsonDiff([], []);

        /** @var AddRemovalsToJsonDiffAction $action */
        $action = Container::getInstance()->make(AddRemovalsToJsonDiffAction::class);

        $action
            ->execute(
                collect(),
                $originalArray,
                $jsonDiff,
                ''
            );

        $this->assertCount(1, $jsonDiff->getValuesRemoved());

        /** @var ValueRemoved $valueRemoved */
        $valueRemoved = $jsonDiff->getValuesRemoved()->first();

        $this->assertSame(
            '1',
            $valueRemoved->getPath()
        );
        $this->assertSame(
            'unmapped',
            $valueRemoved->getValue()
        );
    }
}
