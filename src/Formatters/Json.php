<?php

namespace Differ\Formatters\Json;

function perform(array $diff): string
{
    $json = @json_encode($diff, JSON_PRETTY_PRINT);
    
    if ($json === false) {
        throw new \Exception("Failed to encode data as JSON");
    }
        
    return $json;
}
