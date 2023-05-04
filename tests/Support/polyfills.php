<?php

declare(strict_types=1);

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Container\Container;

if (! function_exists('fake') && class_exists(FakerFactory::class)) {
    /**
     * Get a faker instance.
     */
    function fake(?string $locale = null): FakerGenerator
    {
        $container = Container::getInstance();
        if (! $container->bound('faker')) {
            $container
                ->singleton(
                    'faker',
                    function () {
                        return FakerFactory::create();
                    }
                );
        }

        return $container->make('faker');
    }
}