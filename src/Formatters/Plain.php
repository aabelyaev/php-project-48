<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

function perform(array $diff): string
{
    $formattedDiff = formatPlain($diff, '');
    $flattenedDiff = flatten($formattedDiff);
    $filteredResult = array_filter($flattenedDiff, fn($item) => $item !== null && $item !== false && $item !== '');
    return implode("\n", $filteredResult);
}

function formatPlain(array $diff, string $prefix = ''): array|null
{
    // Проверяем наличие и тип переменной $diff['children']
    if (!isset($diff['children']) || !is_array($diff['children'])) {
        return null; // Возвращаем null, если 'children' не существует или не является массивом
    }

    $result = [];
    foreach ($diff['children'] as $child) {
        $formattedChild = formatPlain($child, $prefix);
        if (!is_null($formattedChild)) {
            $result = array_merge($result, $formattedChild);
        }
    }

    $status = $diff['status'];
    $key = $diff['key'] ?? null;

    switch ($status) {
        case 'root':
            return $result;

        case 'have children':
            $fullPath = ($prefix === '') ? $key : "{$prefix}.{$key}";
            foreach ($diff['children'] as $child) {
                $formattedChild = formatPlain($child, $fullPath);
                if (!is_null($formattedChild)) {
                    $result[] = $formattedChild;
                }
            }
            return $result;

        case 'added':
            $fullPath = ($prefix === '') ? $key : "{$prefix}.{$key}";
            $value = stringify($diff['value']);
            $result[] = "Property '{$fullPath}' was added with value: {$value}";
            return $result;

        case 'unchanged':
            return null;

        case 'removed':
            $fullPath = ($prefix === '') ? $key : "{$prefix}.{$key}";
            $result[] = "Property '{$fullPath}' was removed";
            return $result;

        case 'updated':
            $fullPath = ($prefix === '') ? $key : "{$prefix}.{$key}";
            $value1 = stringify($diff['value1']);
            $value2 = stringify($diff['value2']);
            $result[] = "Property '{$fullPath}' was updated. From {$value1} to {$value2}";
            return $result;

        default:
            throw new \Exception("Unknown status '{$status}'");
    }
}


function stringify(mixed $data): string
{
    if (is_string($data)) {
        return "'$data'";
    } elseif (is_numeric($data)) {
        return (string) $data;
    } elseif (is_bool($data)) {
        return $data ? 'true' : 'false';
    } elseif (is_null($data)) {
        return 'null';
    } elseif (is_object($data)) {
        return '[complex value]';
    }

    $failure = gettype($data);
    throw new \Exception(sprintf('Unknown format %s is given!', $failure));
}
