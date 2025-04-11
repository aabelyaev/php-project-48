<?php

namespace Differ\Formatters\Json;

function perform(array $diff): string
{
    try {
        return json_encode($diff, JSON_THROW_ON_ERROR);
    } catch (\JsonException $e) {
        throw new \Exception("Failed to encode data as JSON: " . $e->getMessage());
    }
}
