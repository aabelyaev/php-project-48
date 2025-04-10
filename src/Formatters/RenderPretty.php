<?php

namespace Gendiff\Formatters\RenderPretty;

function makeIndent(int $depth): string
{
    $step = 4;
    $backStep = 2;
    $indent = $depth * $step - $backStep;
    return str_repeat(' ', $indent);
}


function renderPretty(array $tree, int $level = 1): string
{
    $status = $tree['status'];
    $key = $tree['key'] ?? null;
    $indent = makeIndent($level);

    switch ($tree['type']) {
        case 'root':
            $result = array_map(
                function ($node) {
                    return renderPretty($node);
                },
                $tree['children']
            );
            return implode("\n", $result);

        case 'added':
            $value = stringify($tree['value'], $level);
            return "$indent+ $key: $value";
        case 'removed':
            $value = stringify($tree['value'], $level);
            return "$indent- $key: $value";

        case 'unchanged':
            $value = stringify($tree['value'], $level);
            return "$indent  $key: $value";

        case 'updated':
            $value1 = stringify($tree['value1'], $level);
            $value2 = stringify($tree['value2'], $level);
            return "$indent- $key: $value1\n$indent+ $key: $value2";

        case 'have children':
            $result = array_map(
                function ($child) use ($level) {
                    return renderPretty($child, $level + 1);
                },
                $tree['children']
            );
            $prefinal = implode("\n", $result);
            return "$indent  $key: {\n$prefinal\n$indent  }";

        default:
            throw new \Exception('Unknown status');
    }
}

function stringify(mixed $data, int $level = 1): string
{
    if (is_string($data)) {
        return $data;
    } elseif (is_numeric($data)) {
        return (string) $data;
    } elseif (is_bool($data)) {
        return $data ? 'true' : 'false';
    } elseif (is_null($data)) {
        return 'null';
    } elseif (is_object($data)) {
        $keys = array_keys(get_object_vars($data));

        $preview = array_map(
            function ($key) use ($data, $level) {
                $indent = makeIndent($level + 1);
                $value = stringify($data->$key, $level + 1);
                return "$indent  $key: $value";
            },
            $keys
        );

        $result = implode("\n", $preview);
        $closingIndent = makeIndent($level);
        return "{\n$result\n$closingIndent  }";
    }

    $failure = gettype($data);
    throw new \Exception(sprintf('Unknown format %s is given!', $failure));
}