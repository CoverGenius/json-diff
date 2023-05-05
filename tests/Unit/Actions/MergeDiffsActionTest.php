<?php

declare(strict_types=1);

namespace Jet\Tests\Unit\Actions;

use Illuminate\Container\Container;
use Jet\JsonDiff\Actions\MergeDiffsAction;
use Jet\JsonDiff\JsonDiff;
use Jet\Tests\Factories\JsonDiffFactory;
use Jet\Tests\TestCase;

class MergeDiffsActionTest extends TestCase
{
    public function test_it_merges_all_json_diffs_into_one_json_diff(): void
    {
        $jsonDiffs = JsonDiffFactory::new()->createMany(2);

        /** @var JsonDiff $firstJsonDiff */
        $firstJsonDiff = $jsonDiffs->first();
        /** @var JsonDiff $secondJsonDiff */
        $secondJsonDiff = $jsonDiffs->skip(1)->first();

        /** @var MergeDiffsAction $action */
        $action = Container::getInstance()->make(MergeDiffsAction::class);

        $mergedJsonDiff = $action->execute($jsonDiffs);

        $this->assertSame(
            $firstJsonDiff->getNumberOfChanges() + $secondJsonDiff->getNumberOfChanges(),
            $mergedJsonDiff->getNumberOfChanges()
        );
    }
}
