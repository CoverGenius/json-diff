<?php

declare(strict_types=1);

namespace Jet\Tests;

use Jet\Tests\Traits\InteractsWithContainer;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    use InteractsWithContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->flush();
    }
}
