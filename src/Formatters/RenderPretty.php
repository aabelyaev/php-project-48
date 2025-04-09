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
                return INDENT . "{$node['key']}: " . $newChildren;
            case 'unchanged':
                return $offset . INDENT . "{$node['key']}: " . stringify($node['dataAfter'], $offset, $level);
            case 'changed':
                return $offset
                    . "  + {$node['key']}: "
                    . stringify($node['dataAfter'], $offset, $level)
                    . PHP_EOL
                    . $offset
                    . "  - {$node['key']}: "
                    . stringify($node['dataBefore'], $offset, $level);
            case 'removed':
                return $offset
                    . "  - {$node['key']}: "
                    . stringify($node['dataBefore'], $offset, $level);
            case 'added':
                return $offset
                    . "  + {$node['key']}: "
                    . stringify($node['dataAfter'], $offset, $level);
        }
    }, $tree);

    return "{" . PHP_EOL . implode(PHP_EOL, array_filter($nodesForPretty)) . PHP_EOL . $offset . "}";
}

function stringify($value, $parentOffset = '', $level = 0)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false'; // Преобразование булевых значений в строки
    }

    if (!is_array($value)) {
        return $value; // Возвращение простого значения без изменений, если это не массив
    }

    $offset = str_repeat(INDENT, $level + 1); // Вычисление отступа для вложенных элементов

    $nestedItem = array_map(function ($key) use ($parentOffset, $offset, $value, $level) {
        return "{$parentOffset}{$offset}{$key}: " . stringify($value[$key], INDENT, $level + 1); // Рекурсивный вызов для вложенных массивов с указанием $level
    }, array_keys($value));

    return "{" . PHP_EOL . implode(PHP_EOL, $nestedItem) . PHP_EOL . $parentOffset . "}"; // Формирование строки с отступом для вложенного элемента
}