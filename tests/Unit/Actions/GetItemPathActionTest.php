<?php

declare(strict_types=1);

namespace Jet\Tests\Unit\Actions;

use Jet\JsonDiff\Actions\GetItemPathAction;
use PHPUnit\Framework\TestCase;

class GetItemPathActionTest extends TestCase
{
    public function test_it_returns_the_item_path(): void
    {
        $action = new GetItemPathAction();

        $this->assertSame(
            '0',
            $action->execute('', 0)
        );
        $this->assertSame(
            '0.name',
            $action->execute('0', 'name')
        );
    }
}
