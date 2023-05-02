<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

class GetTraversingPathAction
{
    /**
     * @param string $currentPath
     * @param string|int $key
     * @return string
     */
    public function execute(string $currentPath, $key): string
    {
        return "{$currentPath}{$key}.";
    }
}