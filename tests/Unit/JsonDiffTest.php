<?php

declare(strict_types=1);

namespace Jet\Tests\Unit;

use Jet\JsonDiff\JsonDiff;
use Jet\Tests\TestCase;

class JsonDiffTest extends TestCase
{
    public function test_get_number_of_changes(): void
    {
        $jsonDiff = new JsonDiff([], []);
        $this->assertSame(
            0,
            $jsonDiff->getNumberOfChanges()
        );

        $jsonDiff = new JsonDiff(
            ['apple'],
            ['orange']
        );
        $this->assertSame(
            1,
            $jsonDiff->getNumberOfChanges()
        );

        $jsonDiff->addAddedKey('', '0', 'a string');
        $this->assertSame(
            2,
            $jsonDiff->getNumberOfChanges()
        );

        $jsonDiff->addRemovedKey('', '0', 'a string');
        $this->assertSame(
            3,
            $jsonDiff->getNumberOfChanges()
        );
    }
}
