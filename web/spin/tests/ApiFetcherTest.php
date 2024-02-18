<?php

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Service\ApiFetcherService;

class ApiFetcherTest extends TestCase
{



    public function testFetchDataReturnsArray() {
        $fakeJson = json_encode([
            ['name' => 'New Year', 'date' => '2021-01-01'],
            ['name' => 'Christmas', 'date' => '2021-12-25']
        ]);

        $apiFetcherMock = $this->getMockBuilder(ApiFetcherService::class)
            ->onlyMethods(['getFileContents'])
            ->getMock();

        // mock return our fake JSON
        $apiFetcherMock->method('getFileContents')
            ->willReturn($fakeJson);

        $data = $apiFetcherMock->fetchData();
        $this->assertIsArray($data, "The fetchData method should return an array.");
        $this->assertCount(2, $data, "The returned array should have a count of 2.");
    }


    public function testFetchDataThrowsExceptionWhenUrlIsNotSet()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('API URL is not set.');

        $service = new ApiFetcherService();

        $reflector = new ReflectionClass($service);
        $urlProperty = $reflector->getProperty('url');
        $urlProperty->setValue($service, '');

        $service->fetchData();
    }

}