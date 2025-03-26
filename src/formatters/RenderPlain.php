<?php

namespace Gendiff\Formatters\RenderPlain;

function renderPlain(array $tree)
{
    return buildPlain($tree);
}

function buildPlain($tree, $parent = '')
{
    $nodesForPlain = array_map(function ($node) use ($parent) {
        switch ($node['type']) {
            case 'nested':
                return buildPlain($node['children'], "{$parent}{$node['key']}.");
            case 'changed':
                return sprintf(
                    "Property '{$parent}{$node['key']}' was changed. From '%s' to '%s'",
                    stringify($node['dataBefore']),
                    stringify($node['dataAfter'])
                );
            case 'removed':
                return sprintf("Property '{$parent}{$node['key']}' was removed");
            case 'added':
                return sprintf(
                    "Property '{$parent}{$node['key']}' was added with value: '%s'",
                    stringify($node['dataAfter'])
                );
        }
    }, $tree);

    return implode(PHP_EOL, array_filter($nodesForPlain, fn($item) => !empty($item)));
}

function stringify($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_array($value)) {
        return 'complex value';
    }

    return $value;
}
