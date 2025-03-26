<?php

namespace Gendiff\Formatters\RenderJson;

function renderJson(array $tree)
{
    return json_encode($tree, JSON_PRETTY_PRINT);
}
