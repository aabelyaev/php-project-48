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
            default:
                $str = "Property '%s' %s";
                $action = '';
                switch ($node['type']) {
                    case 'changed':
                        $action = " was changed. From '%s' to '%s'";
                        break;
                    case 'removed':
                        $action = " was removed";
                        break;
                    case 'added':
                        $str .= " was added with value: '%s'";
                        break;
                    default:
                        throw new \Exception("Unknown node {$node}");
                    }
                return sprintf($str, "{$parent}{$node['key']}", $action, stringify($node['dataBefore']), stringify($node['dataAfter']));
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
