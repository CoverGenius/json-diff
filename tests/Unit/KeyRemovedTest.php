<?php

declare(strict_types=1);

namespace Jet\Tests\Unit;

use Jet\JsonDiff\KeyRemoved;
use PHPUnit\Framework\TestCase;

class KeyRemovedTest extends TestCase
{
    public function test_it_returns_the_path(): void
    {
        $keyRemoved = new KeyRemoved('a-path', 'a-name');

        $this->assertSame('a-path', $keyRemoved->getPath());
    }

    public function test_it_returns_the_name(): void
    {
        $keyRemoved = new KeyRemoved('a-path', 'a-name');

        $this->assertSame('a-name', $keyRemoved->getName());
    }
}
