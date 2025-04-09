<?php

namespace Gendiff\Formatters\RenderPretty;

const INDENT = '    ';

function renderPretty(array $tree)
{
    return buildPretty($tree);
}

function buildPretty($tree, $level = 0)
{
    $offset = str_repeat(INDENT, $level);

    $nodesForPretty = array_map(function ($node) use ($offset, $level) {
        switch ($node['type']) {
            case 'nested':
                $newChildren = buildPretty($node['children'], $level + 1);
                return implode('', [INDENT, $node['key'], ': ', $newChildren]);
            case 'unchanged':
                $valueStr = stringify($node['dataAfter']); 
                return implode('', [$offset, INDENT, $node['key'], ': ', $valueStr]);
                case 'changed':
                    $beforeValueStr = stringify($node['dataBefore']);
                    $afterValueStr = stringify($node['dataAfter']);
                    return [
                        sprintf('%s  + %s: %s', $offset, $node['key'], $afterValueStr),
                        "\n",
                        sprintf('%s  - %s: %s', $offset, $node['key'], $beforeValueStr),
                    ];
                
                case 'removed':
                    $valueStr = stringify($node['dataBefore']);
                    return [
                        sprintf('%s  - %s: %s', $offset, $node['key'], $valueStr),
                    ];
                
                case 'added':
                    $valueStr = stringify($node['dataAfter']);
                    return [
                        sprintf('%s  + %s: %s', $offset, $node['key'], $valueStr),
                    ];
                default:
                    return "Unknown node type: {$node['type']}";
        }
    }, $tree);

    $result = implode("\n", array_filter($nodesForPretty));
    
    if ($level == 0) {
        return sprintf('{%s%s%s}', "\n", $result, "\n");
    } else {
        return sprintf('%s{%s%s%s}', $offset, "\n", $result, "\n");
    }
}

function stringify($value, $parentOffset = '', $level = 0)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (!is_array($value)) {
        return $value;
    }

    $offset = str_repeat(INDENT, $level + 1);
    
    $keys = array_keys($value);

    $nestedItems = array_map(function ($key) use ($parentOffset, $offset, $value) {
        $keyStr = $parentOffset ? $parentOffset . "  " : '';
        return "$keyStr$offset{$key}: {$value[$key]}";
    }, $keys);

    $result = implode("\n", $nestedItems);
    
    if ($level == 0 && !empty($parentOffset)) {
        return sprintf('{%s%s%s}', "\n", $result, "\n" . $parentOffset);
    } else {
        return sprintf('{%s%s}', "\n", $offset . "{" . $result . "\n");
    }
}
