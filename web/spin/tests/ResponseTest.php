<?php


use Foundation\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testGetStatusCode()
    {
        $response = new Response(200, '{}', 'OK');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetMsg()
    {
        $response = new Response(200, '{}', 'success message');
        $this->assertEquals('success message', $response->getMsg());
    }

    public function testGetBodySuccess()
    {
        $body = ['key' => 'value'];
        $response = new Response(200, $body);
        $expectedBody = json_encode([
            'status' => 'success',
            'data'   => $body,
        ]);

        $this->assertEquals($expectedBody, $response->getBody());
    }

    public function testGetBodyError()
    {
        $response = new Response(404, [], 'Not Found');
        $expectedBody = json_encode([
            'status' => 'error',
            'msg'    => 'Not Found',
        ]);

        $this->assertEquals($expectedBody, $response->getBody());
    }

    public function testIsSuccessTrue()
    {
        $response = new Response(200, '{}', '');
        $reflectionMethod = new \ReflectionMethod(Response::class, 'isSuccess');

        $this->assertTrue($reflectionMethod->invoke($response));
    }

    public function testIsSuccessFalse()
    {
        $response = new Response(400, '{}', '');
        $reflectionMethod = new \ReflectionMethod(Response::class, 'isSuccess');
        $this->assertFalse($reflectionMethod->invoke($response));
    }
}
