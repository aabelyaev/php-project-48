<?php

namespace Gendiff\Formatters\RenderPretty;

function renderPretty(array $tree)
{
    return buildPretty($tree);
}

function makeIndent(int $depth): string
{
    $step = 4;
    $backStep = 2;
    $indent = $depth * $step - $backStep;
    return str_repeat(' ', $indent);
}


function buildPretty(array $tree, int $level = 1): array
{
    return array_map(function ($node) use ($level) {

        $signRemoved = '  - ';
        $signAdded = '  + ';
        $signNoSign = '    ';
        $indent = str_repeat('    ', $level  - 1);

        switch ($node['status']) {
            case 'unchanged':
                $sign = $signNoSign;
                $value = stringify($node['value'], $level);
                return "{$indent}{$sign}{$node['key']}: {$value}";

            case 'added':
                $sign = $signAdded;
                $value = stringify($node['value'], $level);
                return "{$indent}{$sign}{$node['key']}: {$value}";

            case 'removed':
                $sign = $signRemoved;
                $value = stringify($node['value'], $level);
                return "{$indent}{$sign}{$node['key']}: {$value}";

            case 'updated':
                $oldValue = stringify($node['oldValue'], $level);
                $newValue = stringify($node['newValue'], $level);
                $firstStr = "{$indent}{$signRemoved}{$node['key']}: {$oldValue}";
                $secondStr = "{$indent}{$signAdded}{$node['key']}: {$newValue}";
                return $firstStr . PHP_EOL . $secondStr;

            case 'nested':
                $sign = $signNoSign;
                $children = $node['children'];
                $firstStr = "{$indent}{$sign}{$node['key']}: {";
                $preparedStrings = buildPretty($children, $level + 1);
                $childrenStr = implode(PHP_EOL, $preparedStrings);
                $lastStr = "{$indent}    }";
                return $firstStr . PHP_EOL . $childrenStr . PHP_EOL . $lastStr;

            default:
                throw new \Exception("Unknown node status '{$node['status']}'");
        }
    }, $tree);
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