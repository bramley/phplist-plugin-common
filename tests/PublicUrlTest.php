<?php

use PHPUnit\Framework\TestCase;

use function phpList\plugin\Common\publicBaseUrl;
use function phpList\plugin\Common\publicUrl;

class PublicUrlTest extends TestCase
{
    /**
     * @test
     */
    public function urlWithPageAndParams()
    {
        $result = publicUrl('page1.php', ['param1' => 'foo', 'param2' => 'bar']);
        $expected = 'http://www.mysite.com/lists/page1.php?param1=foo&param2=bar';
        $this->assertEquals($expected, $result);
    }
    /**
     * @test
     */
    public function urlWithParams()
    {
        $result = publicUrl(['param1' => 'foo', 'param2' => 'bar']);
        $expected = 'http://www.mysite.com/lists/?param1=foo&param2=bar';
        $this->assertEquals($expected, $result);
    }

}
