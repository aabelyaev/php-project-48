<?php

namespace Gendiff\Formatters\RenderPretty;

const INDENT = '    '; // Определение константы для отступов

function renderPretty(array $tree)
{
    return buildPretty($tree);
}

function buildPretty($tree, $level = 0)
{
    $offset = str_repeat(INDENT, $level); // Вычисление текущего отступа

    $nodesForPretty = array_map(function ($node) use ($offset, $level) {
        switch ($node['type']) {
            case 'nested':
                $newChildren = buildPretty($node['children'], $level + 1); // Рекурсивный вызов для вложенных узлов
                return "{$offset}  {$node['key']}: {$newChildren}"; // Формирование строки с отступом
            case 'unchanged':
                $valueStr = stringify($node['dataAfter'], INDENT, $level + 1); // Вызов функции для форматирования значения
                return "{$offset}    {$node['key']}: {$valueStr}"; // Формирование строки с отступом
            case 'changed':
                $beforeValueStr = stringify($node['dataBefore'], INDENT, $level + 1); // Вызов функции для форматирования старого значения
                $afterValueStr = stringify($node['dataAfter'], INDENT, $level + 1); // Вызов функции для форматирования нового значения
                return "{$offset}  - {$node['key']}: {$beforeValueStr}\n" . "{$offset}  + {$node['key']}: {$afterValueStr}"; // Формирование строки с изменениями
            case 'removed':
                $valueStr = stringify($node['dataBefore'], INDENT, $level + 1); // Вызов функции для форматирования значения
                return "{$offset}  - {$node['key']}: {$valueStr}"; // Формирование строки с удалением
            case 'added':
                $valueStr = stringify($node['dataAfter'], INDENT, $level + 1); // Вызов функции для форматирования значения
                return "{$offset}  + {$node['key']}: {$valueStr}"; // Формирование строки с добавлением
            default:
                throw new \Exception("Unknown node {$node}");
        }
    }, $tree);

    return "{" . PHP_EOL . implode(PHP_EOL, array_filter($nodesForPretty)) . PHP_EOL . $offset . "}"; // Формирование окончательной строки с отступом
}

function stringify($value, $parentOffset = '', $level = 0)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false'; // Преобразование булевых значений в строки
    }

    if (!is_array($value)) {
        return $value; // Возвращение простого значения без изменений
    }

    $offset = str_repeat(INDENT, $level + 1); // Вычисление отступа для вложенных элементов

    $nestedItem = array_map(function ($key) use ($parentOffset, $offset, $value) {
        return "{$parentOffset}{$offset}{$key}: " . stringify($value[$key], INDENT, $level + 1); // Рекурсивный вызов для вложенных массивов
    }, array_keys($value));

    return "{" . PHP_EOL . implode(PHP_EOL, $nestedItem) . PHP_EOL . $parentOffset . "}"; // Формирование строки с отступом для вложенного элемента
}