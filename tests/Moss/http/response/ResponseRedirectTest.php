<?php
namespace Moss\http\response;


class ResponseRedirectTest extends \PHPUnit_Framework_TestCase {


	public function testAddress() {
		$Response = new ResponseRedirect('http://127.0.0.1/');
		$this->assertEquals('http://127.0.0.1/', $Response->address());
	}

	public function testDelay() {
		$Response = new ResponseRedirect('http://127.0.0.1/', 10);
		$this->assertEquals(10, $Response->delay());
	}
}