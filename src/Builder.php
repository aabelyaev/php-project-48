<?php

namespace Gendiff\Builder;

function builderTree($dataBefore, $dataAfter)
{
    $uniqueKeys = array_values(
        array_unique(array_merge(array_keys($dataBefore), array_keys($dataAfter)))
    );

     return array_map(function ($key) use ($dataBefore, $dataAfter) {
        if (!key_exists($key, $dataBefore)) {
            return buildNode($key, 'added', null, $dataAfter[$key]);
        }

        if (!key_exists($key, $dataAfter)) {
            return buildNode($key, 'removed', $dataBefore[$key]);
        }

        if (is_array($dataBefore[$key]) && is_array($dataAfter[$key])) {
            return buildNode($key, 'nested', null, null, builderTree($dataBefore[$key], $dataAfter[$key]));
        }

        if ($dataBefore[$key] !== $dataAfter[$key]) {
            return buildNode($key, 'changed', $dataBefore[$key], $dataAfter[$key]);
        }

        return buildNode($key, 'unchanged', $dataBefore[$key], $dataAfter[$key]);
     }, $uniqueKeys);
}

function buildNode($key, $type, $dataBefore = null, $dataAfter = null, $children = [])
{
    return [
        'key'           => $key,
        'type'          => $type,
        'dataBefore'    => $dataBefore,
        'dataAfter'     => $dataAfter,
        'children'      => $children
    ];
}