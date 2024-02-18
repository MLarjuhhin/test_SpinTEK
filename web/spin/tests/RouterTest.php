<?php


use Dotenv\Dotenv;
use Foundation\Http\Response;
use Foundation\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{


    public function testHandleGetRequestForHolidays()
    {
        $router = new Router();

        $response = $router->handle('/schedule', 'GET');

        $this->assertInstanceOf(Response::class, $response);
       $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());
        $this->assertArrayHasKey('data', json_decode($response->getBody(),true));

    }

    public function testHandleGetRequestForNonExistentRoute()
    {
        $router = new Router();

        $response = $router->handle('/nonexistent', 'GET');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $bodyArray = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('msg', $bodyArray,"The response does not contain the 'msg' key.");
    }

    public function testHandlePostRequestForNonExistentRoute()
    {
        $router = new Router();

        $response = $router->handle('/nonexistent', 'POST');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $bodyArray = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('status', $bodyArray, "The response does not contain the 'status' key.");
        $this->assertArrayHasKey('msg',$bodyArray,"The response does not contain the 'msg' key.");

        $this->assertEquals('error', $bodyArray['status'], "The value of the 'status' key is not equal to 'error'.");

    }


}