<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $fileContent, string $fileFormat): object
{
    switch ($fileFormat) {
        case 'json':
            return json_decode($fileContent);

        case 'yaml':
        case 'yml':
            return Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);

        default:
            throw new \Exception("Unknown.{$fileFormat} extension.");
    }
}
