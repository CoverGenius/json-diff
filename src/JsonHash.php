<?php

declare(strict_types=1);

namespace Jet\JsonDiff;

class JsonHash
{
    public function xorHash($data, string $path = ''): string
    {
        $xorHash = '';

        if (!is_array($data)) {
            $s = $path . $data;
            if (strlen($xorHash) < strlen($s)) {
                $xorHash = str_pad($xorHash, strlen($s));
            }
            $xorHash ^= $s;

            return $xorHash;
        }

        foreach ($data as $key => $item) {
            $itemPath = $path . '/' . $key;
            $itemHash = $path . $this->xorHash($item, $itemPath);
            if (strlen($xorHash) < strlen($itemHash)) {
                $xorHash = str_pad($xorHash, strlen($itemHash));
            }
            $xorHash ^= $itemHash;
        }

        return $xorHash;
    }
}
