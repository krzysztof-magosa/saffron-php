<?php
/**
 * Copyright 2014 Krzysztof Magosa
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
use \KM\Saffron\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testDomain()
    {
        $request = new Request();
        $request->setDomain('example.com');
        $this->assertEquals('example.com', $request->getDomain());
    }

    public function testUri()
    {
        $request = new Request();
        $request->setUri('/test/uri');
        $this->assertEquals('/test/uri', $request->getUri());
    }

    public function testMethod()
    {
        $request = new Request();
        $request->setMethod('GET');
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testHttps()
    {
        $request = new Request();
        $request->setHttps(true);
        $this->assertEquals(true, $request->getHttps());
    }

    public function testGlobals1()
    {
        $_SERVER['REQUEST_URI'] = '/another/uri';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS'] = 'on';
  
        $request = Request::createFromGlobals();   
        $this->assertEquals('/another/uri', $request->getUri());
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('example.com', $request->getDomain());
        $this->assertEquals(true, $request->getHttps());
    }

    public function testGlobals2()
    {
        $_SERVER['REQUEST_URI'] = '/another/uri';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS'] = 'off';
        $request = Request::createFromGlobals();   
        $this->assertEquals(false, $request->getHttps());
    }

    public function testGlobals3()
    {
        $_SERVER['REQUEST_URI'] = '/another/uri';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS'] = '';
        $request = Request::createFromGlobals();   
        $this->assertEquals(false, $request->getHttps());
    }
}
