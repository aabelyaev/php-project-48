<?php

namespace Differ\Formatters\RenderJson;

function perform(array $diff): string
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
