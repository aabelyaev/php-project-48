<?php

namespace Differ\Formatters\Json;

function perform(array $diff): string
{
    $json = @json_encode($diff, JSON_THROW_ON_ERROR);
    if ($json === false) {
        throw new \Exception("Failed to encode data as JSON");
    }
    return $json;
}
