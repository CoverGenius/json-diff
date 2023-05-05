<?php

declare(strict_types=1);

namespace Jet\JsonDiff\Actions;

class GetItemPathAction
{
    /**
     * @param int|string $key
     */
    public function execute(string $currentPath, $key): string
    {
        if ($currentPath === '') {
            return "{$key}";
        }

        return "{$currentPath}.{$key}";
    }
}
