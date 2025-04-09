<?php

namespace Gendiff\Formatters\RenderPlain;

function renderPlain(array $tree)
{
    return buildPlain($tree);
}

function buildPlain(array $tree, string $parent = '')
{
    $nodesForPlain = array_map(function ($node) use ($parent) {
        switch ($node['type']) {
            case 'nested':
                return buildPlain($node['children'], "{$parent}{$node['key']}.");
            default:
                return formatNode($node, $parent);
        }
    }, $tree);
    return implode(PHP_EOL, array_filter($nodesForPlain, fn($item) => !empty($item)));
}

function formatNode(array $node, string $parent): string
{
    $messageTemplate = "Property '%s' %s";
    $action = '';
    switch ($node['type']) {
        case 'changed':
            $action = sprintf("was changed. From '%s' to '%s'", stringify($node['dataBefore']), stringify($node['dataAfter']));
            break;
        case 'removed':
            $action = "was removed";
            break;
        case 'added':
            $action = sprintf("was added with value: '%s'", stringify($node['dataAfter']));
            break;
        default:
            throw new \Exception("Unknown node type: {$node['type']}");
    }
    return sprintf($messageTemplate, "{$parent}{$node['key']}", $action);
}

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