<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

class GetTraversingPathAction
{
    public function execute(string $currentPath, string $key): string
    {
        return "{$currentPath}{$key}.";
    }
}