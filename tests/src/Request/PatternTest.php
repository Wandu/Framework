<?php
//namespace June\Request;
//
//use PHPUnit_Framework_TestCase;
//
//class PatternTest extends PHPUnit_Framework_TestCase
//{
//    public function testParseUri()
//    {
//
//        $data1 = [
//            'path' => ''
//        ];
//        $data2 = [
//            'path' => '/'
//        ];
//        $data3 = [
//            'path' => 'test/'
//        ];
//        $data4 = [
//            'path' => '/test/'
//        ];
//        $data5 = [
//            'path' => 'test/a'
//        ];
//        $data6 = [
//            'path' => 'test/a;data1;data2'
//        ];
//        $data7 = [
//            'path' => 'test/a?value=1&value=2'
//        ];
//
//        $pattern1 = new Pattern($data1);
//        $pattern2 = new Pattern($data2);
//        $pattern3 = new Pattern($data3);
//        $pattern4 = new Pattern($data4);
//        $pattern5 = new Pattern($data5);
//        $pattern6 = new Pattern($data6);
//        $pattern7 = new Pattern($data7);
//
//        $this->assertEquals('', $pattern1->getUri());
//        $this->assertEquals('', $pattern2->getUri());
//        $this->assertEquals('test', $pattern3->getUri());
//        $this->assertEquals('test', $pattern4->getUri());
//        $this->assertEquals('test/a', $pattern5->getUri());
//        $this->assertEquals('test/a', $pattern6->getUri());
//        $this->assertEquals('test/a', $pattern7->getUri());
//    }
//
//    public function testParseArgsWithPattern()
//    {
//        $data1 = [
//            'path' => '/'
////            'method' => 'GET'
//        ];
//        $data2 = [
//            'path' => '/test/'
////            'method' => 'POST',
////            'uri' => '/test'
//        ];
//        $data3 = [
//            'path' => '/test/{id}'
////            'method' => 'POST',
////            'uri' => '/test/{id}'
//        ];
//        $data4 = [
//            'path' => '/test/{page}/{num}'
////            'method' => 'POST',
////            'uri' => '/test/{page}/{num}'
//        ];
//
//        $pattern1 = new Pattern($data1);
//        $pattern2 = new Pattern($data2);
//        $pattern3 = new Pattern($data3);
//        $pattern4 = new Pattern($data4);
//
//        $this->assertEquals(array(), $pattern1->getArgs());
//        $this->assertEquals('', $pattern1->getPattern());
//
//        $this->assertEquals(array(), $pattern2->getArgs());
//        $this->assertEquals('test', $pattern2->getPattern());
//
//        $this->assertEquals(array(), $pattern3->getArgs());
//        $this->assertEquals("test\/(?<id>[^\/\#]+)", $pattern3->getPattern());
//
//        $this->assertEquals(array(), $pattern4->getArgs());
//        $this->assertEquals("test\/(?<page>[^\/\#]+)\/(?<num>[^\/\#]+)", $pattern4->getPattern());
//    }
//}
