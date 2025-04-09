<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiffPrettyFormat()
    {
        $expected = file_get_contents($this->getPath('rightPretty'));
        $this->assertEquals($expected, genDiff($this->getPath('before.json'), $this->getPath('after.json')));
    }

    public function testGenDiffPlainFormat()
    {
        $expected = file_get_contents($this->getPath('rightPlainTree'));
        $this->assertEquals($expected, genDiff($this->getPath('before.yml'), $this->getPath('after.yml'), 'plain'));
    }

    public function testGenDiffJsonFormat()
    {
        $expected = file_get_contents($this->getPath('rightJsonTree.json'));
        $this->assertEquals($expected, genDiff($this->getPath('before.json'), $this->getPath('after.json'), 'json'));
    }

    protected function getPath($filename)
    {
        return __DIR__ . "/fixtures/{$filename}";
    }
}
