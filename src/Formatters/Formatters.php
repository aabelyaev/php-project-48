<?php

namespace Gendiff\Formatters\Formatters;

use function Gendiff\Formatters\RenderJson\renderJson;
use function Gendiff\Formatters\RenderPlain\renderPlain;
use function Gendiff\Formatters\RenderPretty\renderPretty;

function renderDiff($format, $tree)
{
    switch ($format) {
        case 'pretty':
            return renderPretty($tree);
        case 'plain':
            return renderPlain($tree);
        case 'json':
            return renderJson($tree);
        default:
            throw new \Exception("Unknown format {$format}");
    }
}
