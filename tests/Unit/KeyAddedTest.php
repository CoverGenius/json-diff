<?php

declare(strict_types=1);

namespace Jet\Tests\Unit;

use Jet\JsonDiff\KeyAdded;
use PHPUnit\Framework\TestCase;

class KeyAddedTest extends TestCase
{
    public function test_it_returns_the_path(): void
    {
        $keyAdded = new KeyAdded('a-path', 'a-name');

        $this->assertSame('a-path', $keyAdded->getPath());
    }

    public function test_it_returns_the_name(): void
    {
        $keyAdded = new KeyAdded('a-path', 'a-name');

        $this->assertSame('a-name', $keyAdded->getName());
    }
}
