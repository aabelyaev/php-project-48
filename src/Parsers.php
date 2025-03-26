<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($extension, $content)
{
    switch ($extension) {
        case 'json':
            return json_decode($content, true);
        case 'yml':
        case 'yaml':
            return Yaml::parse($content);
        default:
            throw new \Exception("unknown file extension {$extension}");
    }
}