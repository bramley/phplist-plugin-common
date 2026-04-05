<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function phpList\plugin\Common\splitIntoLines;

class SplitIntoLinesTest extends TestCase
{
    public static function lineEndingsDataProvider()
    {
        return [
            'line-ending CRNL' => ["element 1\r\nelement 2\r\nelement 3\r\n"],
            'line-ending NL' => ["element 1\nelement 2\nelement 3\n"],
            'line-ending CR' => ["element 1\relement 2\relement 3\r"],
        ];
    }

    #[DataProvider('lineEndingsDataProvider')]
    public function testLineEndings($value)
    {
        $result = splitIntoLines($value);
        $expected = ['element 1', 'element 2', 'element 3'];
        $this->assertEquals($expected, $result);
    }

    public static function trimValueDataProvider()
    {
        return [
            'trailing line ending' => ["element 1\relement 2\relement 3\r\r"],
            'leading line ending' => ["\relement 1\relement 2\relement 3\r"],
            'leading space' => [" element 1 \relement 2 \relement 3 \r"],
            'trailing space' => ["element 1 \relement 2 \relement 3 \r "],
            'line with only spaces' => ["element 1 \r    \relement 2 \relement 3 \r"],
        ];
    }

    #[DataProvider('trimValueDataProvider')]
    public function testTrimValue($value)
    {
        $result = splitIntoLines($value);
        $expected = ['element 1', 'element 2', 'element 3'];
        $this->assertEquals($expected, $result);
    }

    public function testSingleLine()
    {
        $result = splitIntoLines('element 1');
        $expected = ['element 1'];
        $this->assertEquals($expected, $result);
    }

    public function testEmptyValue()
    {
        $result = splitIntoLines('');
        $expected = [];
        $this->assertEquals($expected, $result);
    }
}
