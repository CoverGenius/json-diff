<?php

declare(strict_types=1);

namespace Jet\Tests\Traits;

use Illuminate\Container\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;

trait InteractsWithContainer
{
    protected function stub(string $className): Stub
    {
        return tap(
            $this->createStub($className),
            function (Stub $stub) use ($className) {
                Container::getInstance()
                    ->bind(
                        $className,
                        function () use ($stub) {
                            return $stub;
                        }
                    );
            }
        );
    }

    protected function mock($className): MockObject
    {
        return tap(
            $this->createMock($className),
            function (MockObject $stub) use ($className) {
                Container::getInstance()
                    ->bind(
                        $className,
                        function () use ($stub) {
                            return $stub;
                        }
                    );
            }
        );
    }
}