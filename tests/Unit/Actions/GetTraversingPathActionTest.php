<?php

declare(strict_types=1);

namespace Jet\Tests\Unit\Actions;

use Jet\JsonDiff\Actions\GetTraversingPathAction;
use PHPUnit\Framework\TestCase;

class GetTraversingPathActionTest extends TestCase
{
    public function test_it_returns_base_path_when_traversing_into_an_array(): void
    {
        $action = new GetTraversingPathAction();

        $this->assertSame(
            '0.',
            $action->execute('', 0)
        );
        $this->assertSame(
            '0.sports.',
            $action->execute('0.', 'sports')
        );
    }
}
