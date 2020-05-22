<?php

require __DIR__ . '/../plugins/Common/StringStream.php';

use phpList\plugin\Common\StringStream;

class StringStreamTest extends PHPUnit\Framework\TestCase
{
    public function testOpenStringAsFileHandle()
    {
        $string = "hello again\n";
        $fh = StringStream::fopen($string);
        self::assertIsResource($fh);
    }

    public function testReadStringAsFile()
    {
        $string = "hello again\n";
        $fh = StringStream::fopen($string);
        $line = fgets($fh);
        $this->assertEquals($string, $line);
        $line = fgets($fh);
        $this->assertEquals(false, $line);
    }

    public function testReadStringAsStream()
    {
        $string = "hello there\n";
        $id = StringStream::stringId($string);
        $this->assertEquals($string, file_get_contents("string://$id"));
    }

    public function testReadCsvString()
    {
        $csv = <<<'END'
aaaa,bbb
ccc,ddd
e,f
END;
        $fh = StringStream::fopen($csv);
        $fields = fgetcsv($fh);
        $this->assertEquals(['aaaa', 'bbb'], $fields);
        $fields = fgetcsv($fh);
        $this->assertEquals(['ccc', 'ddd'], $fields);
        $fields = fgetcsv($fh);
        $this->assertEquals(['e', 'f'], $fields);
    }

    public function testReadEmptyString()
    {
        $string = '';
        $fh = StringStream::fopen($string);
        $line = fread($fh, 1000);
        $this->assertSame('', $line);
        $eof = feof($fh);
        $this->assertTrue($eof);
    }

    public function testGetEmptyString()
    {
        $string = '';
        $fh = StringStream::fopen($string);
        $line = fgets($fh);
        $this->assertSame(false, $line);
        $eof = feof($fh);
        $this->assertTrue($eof);
    }

    public function testWriteToStringAsStream()
    {
        $string = '';
        $output = 'here is the output';
        $id = StringStream::stringId($string);
        file_put_contents("string://$id", $output);
        $this->assertEquals($output, $string);
    }

    public function testWriteToStringAsFile()
    {
        $string = '';
        $output = 'here is the output';
        $fh = StringStream::fopen($string, 'w');
        fwrite($fh, $output);
        fclose($fh);
        $this->assertEquals($output, $string);
    }

    public function testOpenTwoFileHandles()
    {
        $string1 = "string 1 line 1\nstring 1 line 2\n";
        $string2 = "string 2 line 1\nstring 2 line 2\n";
        $fh1 = StringStream::fopen($string1);
        $fh2 = StringStream::fopen($string2);

        $line = fgets($fh1);
        $this->assertEquals("string 1 line 1\n", $line);
        $line = fgets($fh2);
        $this->assertEquals("string 2 line 1\n", $line);
        $line = fgets($fh1);
        $this->assertEquals("string 1 line 2\n", $line);
        $line = fgets($fh2);
        $this->assertEquals("string 2 line 2\n", $line);
    }
}
