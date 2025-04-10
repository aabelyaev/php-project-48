<?php

namespace Gendiff\Formatters\Formatters;

use function Gendiff\Formatters\RenderJson\renderJson;
use function Gendiff\Formatters\RenderPlain\renderPlain;
use function Gendiff\Formatters\RenderPretty\renderPretty;

function renderDiff($format, $diff)
{
    switch ($format) {
        case 'pretty':
            return renderPretty($diff);
        case 'plain':
            return renderPlain($diff);
        case 'json':
            return renderJson($diff);
        default:
            throw new \Exception("Unknown format {$format}");
    }
}
