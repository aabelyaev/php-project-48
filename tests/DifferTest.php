<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    #[DataProvider('additionProvider')]
    public function testGenDiff($filename1, $filename2, $format, $expected)
    {
        $file1 = $this->getFixturePath($filename1);
        $file2 = $this->getFixturePath($filename2);
        $expectedFile = file_get_contents($this->getFixturePath($expected));

        $this->assertEquals($expectedFile, genDiff($file1, $file2, $format));
    }

    public static function additionProvider(): array
    {
        $filename1 = 'after.json';
        $filename2 = 'before.json';
        $filename4 = 'after.yml';
        $filename5 = 'before.yaml';
        $formatStylish = 'stylish';
        $formatPlain = 'plain';
        $formatJson = 'json';
        $expectedStylish = 'stylishExpected.txt';
        $expectedPlain = 'plainExpected.txt';
        $expectedJson = 'jsonExpected.txt';

        return [
            'json to json. Format Stylish' => [$filename1, $filename2, $formatStylish, $expectedStylish],
            'yml to yaml. Format Stylish' => [$filename4, $filename5, $formatStylish, $expectedStylish],
            'json to yaml. Format Stylish' => [$filename1, $filename5, $formatStylish, $expectedStylish],
            'json to json. Format Plain' => [$filename1, $filename2, $formatPlain, $expectedPlain],
            'yml to yaml. Format Plain' => [$filename4, $filename5, $formatPlain, $expectedPlain],
            'json to yaml. Format Plain' => [$filename1, $filename5, $formatPlain, $expectedPlain],
            'json to json. Format Json' => [$filename1, $filename2, $formatJson, $expectedJson],
            'yml to yaml. Format Json' => [$filename4, $filename5, $formatJson, $expectedJson],
            'json to yaml. Format Json' => [$filename1, $filename5, $formatJson, $expectedJson]
        ];
    }

    public function testException1(): void
    {
        $this->expectException(\Exception::class);

        $filename2 = 'before.json';
        $filename1 = 'before7.yml';
        $format = 'stylish';

        genDiff($this->getFixturePath($filename1), $this->getFixturePath($filename2), $format);
    }

    public function testException2(): void
    {
        $this->expectException(\Exception::class);

        $filename2 = 'before.json';
        $filename1 = 'testFile3.txt';
        $format = 'stylish';

        genDiff($this->getFixturePath($filename1), $this->getFixturePath($filename2), $format);
    }

    public function getFixturePath(string $filename): string
    {
        return __DIR__ . "/fixtures/{$filename}";
    }
}
