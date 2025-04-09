It appears you're looking to modify the data type of the node parameter in your buildPretty function to accept a more flexible type, specifically mixed. This would allow the function to handle different types of input more gracefully. Below, I've updated your code accordingly while ensuring it maintains its functionality and aligns with good coding practices.
Here's the revised version of your PHP code with the changes reflecting mixed type for the node parameter:
<?php

namespace Gendiff\Formatters\RenderPretty;

const INDENT = '    ';

function renderPretty(array $tree)
{
    return buildPretty($tree);
}

/**
 * Recursively builds a pretty formatted string representation of the tree.
 *
 * @param mixed $tree The tree structure made of nodes.
 * @param int $level The current nesting level.
 * @return string The formatted string representation of the tree.
 */
function buildPretty($tree, int $level = 0): string
{
    $offset = str_repeat(INDENT, $level);

    $nodesForPretty = array_map(function ($node) use ($offset, $level) {
        switch ($node['type']) {
            case 'nested':
                $newChildren = buildPretty($node['children'], $level + 1);
                return INDENT . "{$node['key']}: {$newChildren}";
            case 'unchanged':
                $valueStr = stringify($node['dataAfter']); 
                return "{$offset}" . INDENT . "{$node['key']}: {$valueStr}";
            case 'changed':
                $beforeValueStr = stringify($node['dataBefore']);
                $afterValueStr = stringify($node['dataAfter']);
                return "{$offset}  + {$node['key']}: {$afterValueStr}\n{$offset}  - {$node['key']}: {$beforeValueStr}";
            case 'removed':
                $valueStr = stringify($node['dataBefore']);
                return "{$offset}  - {$node['key']}: {$valueStr}";
            case 'added':
                $valueStr = stringify($node['dataAfter']);
                return "{$offset}  + {$node['key']}: {$valueStr}";
            default:
                throw new \Exception("Unknown node type {$node['type']} at level {$level}");
        }
    }, $tree);

    $result = implode("\n", array_filter($nodesForPretty));

    if ($level == 0) {
        return "{\n{$result}\n}";
    }

    return $result;
}

/**
 * Converts a value to its string representation.
 *
 * @param mixed $value The value to convert.
 * @return string The string representation of the value.
 */
function stringify($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_array($value)) {
        return 'complex value';
    }

    return (string)$value;
}