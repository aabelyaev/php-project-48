<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Parsers\parse;
use function Differ\Formatters\format;

function genDiff(string $filename1, string $filename2, string $format = 'pretty'): string
{
    $firstData = getFileContent($filename1); 
    $secondData = getFileContent($filename2); 

    $firstParserResult = parse($firstData, pathinfo($filename1, PATHINFO_EXTENSION)); 
    $secondParserResult = parse($secondData, pathinfo($filename2, PATHINFO_EXTENSION));

    $diff = makeDiff($firstParserResult, $secondParserResult);

    $result = format($diff, $format);

    return $result;
}

function getFileContent(string $path): string
{
    if (!file_exists($path)) {
        throw new \Exception("Oops! No file {$path}!");
    }

    return file_get_contents($path);
}


function differ(object $array1, object $array2): array
{
    $keys = array_unique(array_merge(array_keys(get_object_vars($array1)), array_keys(get_object_vars($array2))));

    $sortedKeys = sort(
        $keys,
        function ($a, $b) {
            return strcmp($a, $b);
        }
    );

    $result = array_map(function ($key) use ($array1, $array2) {
        $value1 = $array1->$key ?? null;
        $value2 = $array2->$key ?? null;

        if (property_exists($array1, $key) && (!property_exists($array2, $key))) {
            return [
                'status' => 'removed',
                'key' => $key,
                'value' => $value1
            ];
        }

        if (!property_exists($array1, $key) && (property_exists($array2, $key))) {
            return [
                'status' => 'added',
                'key' => $key,
                'value' => $value2
            ];
        }

        if ($value1 === $value2) {
            return [
                'status' => 'unchanged',
                'key' => $key,
                'value' => $value1
            ];
        }

        if (is_object($value1) && is_object($value2)) {
            return [
                'status' => 'have children',
                'key' => $key,
                'children' => differ($value1, $value2)
                ];
        }

        return [
            'status' => 'updated',
            'key' => $key,
            'value1' => $value1,
            'value2' => $value2
            ];
    }, $sortedKeys);

    return $result;
}

function makeDiff(object $array1, object $array2): array
{
    return [
        'status' => 'root',
        'children' => differ($array1, $array2)
    ];
}
