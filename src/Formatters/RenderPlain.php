<?php

namespace Gendiff\Formatters\RenderPlain;

function renderPlain(array $tree)
{
    return buildPlain($tree);
}

<?php

namespace Gendiff\Formatters\RenderPlain;

/**
 * Renders the differences in a plain text format.
 *
 * @param array $tree The tree structure representing the differences.
 * @return string
 */
function renderPlain(array $tree)
{
    return buildPlain($tree);
}

/**
 * Recursively builds the plain text representation of the difference tree.
 *
 * @param array $tree The current level of the difference tree.
 * @param string $parent The parent property path.
 * @return string
 */
function buildPlain(array $tree, string $parent = '')
{
    // Map through each node in the tree to build their string representations
    $nodesForPlain = array_map(function ($node) use ($parent) {
        switch ($node['type']) {
            case 'nested':
                return buildPlain($node['children'], "{$parent}{$node['key']}.");
            default:
                return formatNode($node, $parent);
        }
    }, $tree);

    // Filter out empty items and return the results as a single string
    return implode(PHP_EOL, array_filter($nodesForPlain, fn($item) => !empty($item)));
}

/**
 * Formats a single node into a string representation based on its type.
 *
 * @param array $node The node to format.
 * @param string $parent The parent property path.
 * @return string
 */
function formatNode(array $node, string $parent): string
{
    // Establish the base message structure
    $messageTemplate = "Property '%s' %s";
    $action = '';

    // Determine the appropriate action based on node type
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

    // Return the formatted message
    return sprintf($messageTemplate, "{$parent}{$node['key']}", $action);
}

/**
 * Converts values to a string representation.
 *
 * @param mixed $value The value to stringify.
 * @return string
 */
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