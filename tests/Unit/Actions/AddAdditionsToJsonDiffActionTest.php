<?php

declare(strict_types=1);

namespace Jet\Tests\Unit\Actions;

use Illuminate\Container\Container;
use Jet\JsonDiff\Actions\AddAdditionsToJsonDiffAction;
use Jet\JsonDiff\Actions\GetUnmappedNewIndexesAction;
use Jet\JsonDiff\JsonDiff;
use Jet\Tests\Traits\InteractsWithContainer;
use PHPUnit\Framework\TestCase;

class AddAdditionsToJsonDiffActionTest extends TestCase
{
    use InteractsWithContainer;

    public function test_it_adds_additions_to_a_json_diff(): void
    {
        $newArray = [
            0 => 'mapped',
            1 => 'unmapped',
        ];

        $this
            ->stub(GetUnmappedNewIndexesAction::class)
            ->method('execute')
            ->willReturn(
                collect([1])
            );

        $jsonDiff = new JsonDiff([], []);

        /** @var AddAdditionsToJsonDiffAction $action */
        $action = Container::getInstance()->make(AddAdditionsToJsonDiffAction::class);

        $action
            ->execute(
                collect(),
                $newArray,
                $jsonDiff,
                ''
            );

        $this->assertCount(1, $jsonDiff->getValuesAdded());
    }
}
