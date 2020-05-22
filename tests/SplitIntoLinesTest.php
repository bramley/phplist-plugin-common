<?php

use PHPUnit\Framework\TestCase;
use function phpList\plugin\Common\splitIntoLines;

require __DIR__ . '/../plugins/CommonPlugin/functions.php';

class SplitIntoLinesTest extends TestCase
{
    public function lineEndingsDataProvider()
    {
        return [
            'line-ending CRNL' => ["element 1\r\nelement 2\r\nelement 3\r\n"],
            'line-ending NL' => ["element 1\nelement 2\nelement 3\n"],
            'line-ending CR' => ["element 1\relement 2\relement 3\r"],
        ];
    }

    /**
     * @test
     * @dataProvider lineEndingsDataProvider
     */
    public function lineEndings($value)
    {
        $result = splitIntoLines($value);
        $expected = ['element 1', 'element 2', 'element 3'];
        $this->assertEquals($expected, $result);
    }

    public function trimValueDataProvider()
    {
        return [
            'trailing line ending' => ["element 1\relement 2\relement 3\r\r"],
            'leading line ending' => ["\relement 1\relement 2\relement 3\r"],
            'leading space' => [" element 1 \relement 2 \relement 3 \r"],
            'trailing space' => ["element 1 \relement 2 \relement 3 \r "],
            'line with only spaces' => ["element 1 \r    \relement 2 \relement 3 \r"],
        ];
    }

    /**
     * @test
     * @dataProvider trimValueDataProvider
     */
    public function trimValue($value)
    {
        $result = splitIntoLines($value);
        $expected = ['element 1', 'element 2', 'element 3'];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function singleLine()
    {
        $result = splitIntoLines('element 1');
        $expected = ['element 1'];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function emptyValue()
    {
        $result = splitIntoLines('');
        $expected = [];
        $this->assertEquals($expected, $result);
    }
}
