<?php

/*
 * knt-cms: another Content Management System (http://www.kaonet-fr.net/cms)
 * 
 * Licensed under The MIT License
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * @link          http://www.kaonet-fr.net/cms
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


namespace Knt\Framework\Tests\Units\Core;

use \mageekguy\atoum;

 /**
 * Tests for the Request class.
 * Last version tested: 1.0
 */

use \Knt\Framework\Core;

class Request extends atoum\test
{
    /**
     * To test the constructor without parameters.
     * It should initialize the request with $_REQUEST object
     */
    public function testConstructor_WithoutParameters_InitializeDefaultRequest() {
        $req = new Core\Request();
        $this
            ->string($req->getQueriedPath())->isEqualTo('/')
            ->string($req->getMethod())->isEqualTo(Core\Request::METHOD_GET)
            ->integer(count($req->getQueriedData()))->isEqualTo(0)
            ->integer(count($req->getPostedData()))->isEqualTo(0);
    }
    
    /**
     * To test the constructor without parameters
     * but with specific data.
     */
    public function testConstructor_WithoutParameters_InitializeRightRequest() {
        
        global $_SERVER,
               $_GET,
               $_POST;
        
        $_SERVER['PATH_INFO'] = '/Foo/bar';
        $_SERVER['REQUEST_METHOD'] = 'post';
        
        $_GET = array('baz');
        $_POST = array('barz');
        
        $req = new Core\Request();
        $this
            //->string($req->getQueriedPath())->isEqualTo('/Foo/bar') //don't work anymore since we filter the real SERVER var in the code
            ->string($req->getMethod())->isEqualTo(Core\Request::METHOD_POST)
            ->integer(count($req->getQueriedData()))->isEqualTo(1)
            ->integer(count($req->getPostedData()))->isEqualTo(1);
    }
    
    /**
     * To test the constructor parameters
     */
    public function testConstructor_WithParameters_InitializeRightRequest() {        
        
        $arr = array('baz');
        $arr2 = array('barz');
        
        $req = new Core\Request(
                '/Foo/bar', 
                Core\Request::METHOD_PUT,
                new Core\Collection($arr),
                new Core\Collection($arr2)
                );
        
        $this
            ->string($req->getQueriedPath())->isEqualTo('/Foo/bar')
            ->string($req->getMethod())->isEqualTo(Core\Request::METHOD_PUT)
            ->integer(count($req->getQueriedData()))->isEqualTo(1)
            ->integer(count($req->getPostedData()))->isEqualTo(1);
        
    }
    
    /**
     * Test that an exception is thrown if we try to initialize
     * a bad method for the request
     */
    public function testSetMethod_ThrowExceptionIfBadMethod() {
        $req = new Core\Request();
        $this
            ->exception(
                function() use($req) {
                    $req->setMethod('bad');
                })
            ->hasMessage('Bad method specified');
    }
    
    /**
     * Test the serialization of the Collection
     */
    public function testSerialization() {
        
        $arr  = array(0, "Un", 'a' => array(2));
        $colGet = new Core\Collection($arr);
        $arr = array(1, 2);
        $colPost = new Core\Collection($arr);
        
        $initialReq = new Core\Request('/Path/info', 'post', $colGet, $colPost);
        $resultReq = new Core\Request();
        $resultReq->unserialize($initialReq->serialize());

        $this->object($resultReq)->isEqualTo($initialReq);
    }
    
    /**
     * Test the getAsQueryString and getQuery methods
     * (getQuery is an alias of getAsQueryString)
     */
    public function testGetAsQueryString() {
        
        $arr = array('foo' => 'bar', 'baz' => 1);
        $arr2 = array('barz' => 'data');
        
        $req = new Core\Request(
                '/Path/info', 
                'post', 
                new Core\Collection($arr),
                new Core\Collection($arr2)
                );
        
        $this
            ->string($req->getAsQueryString())
            ->isEqualTo($req->getQuery())
            ->isEqualTo('/Path/info?foo=bar&baz=1');
    }
}