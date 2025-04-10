<?php

namespace Differ\Formatters;

function format(array $diff, string $format): string
{
    switch ($format) {
        case 'pretty':
            return RenderPretty\perform($diff);
        case 'plain':
            return RenderPlain\perform($diff);
        case 'json':
            return RenderJson\perform($diff);
        default:
            throw new \Exception("Unknown format '{$format}'.");
    }
}
