<?php

use PHPUnit\Framework\TestCase;
use Service\HolidayManagerService;
use Service\FileStorageService;
use Interface\HolidayDataFetcherInterface;

class HolidayManagerServiceTest extends TestCase
{
    public function testGetHolidaysDataIsUpToDate()
    {
        $year = '2020';
        $holidays = [
            ['date' => '2020-01-01'],
            ['date' => '2020-12-25'],
        ];

        $storageMock = $this->createMock(FileStorageService::class);
        $storageMock->method('isFileValid')->willReturn(true);
        $storageMock->method('getDataFromFile')->willReturn($holidays);

        $fetcherMock = $this->createMock(HolidayDataFetcherInterface::class);

        $service = new HolidayManagerService($storageMock, $fetcherMock);

        $result = $service->getHolidays($year);

        $this->assertNotEmpty($result, "The result not empty");
        $this->assertArrayHasKey($year, $result, "In array not exists year $year.");

    }

    public function testGetHolidaysDataIsOutdated()
    {
        $year = '2021';
        $fetchedHolidays = [
            ['date' => '2021-01-01'],
            ['date' => '2021-12-25'],
        ];

        $storageMock = $this->createMock(FileStorageService::class);
        $storageMock->method('isFileValid')->willReturn(false);
        $storageMock->expects($this->once())->method('saveDataToFile');

        $fetcherMock = $this->createMock(HolidayDataFetcherInterface::class);
        $fetcherMock->method('fetchData')->willReturn($fetchedHolidays);

        $service = new HolidayManagerService($storageMock, $fetcherMock);

        $result = $service->getHolidays($year);

        $this->assertNotEmpty($result,"The result not empty");
        $this->assertArrayHasKey($year, $result, "In results not exists year $year.");
    }

    public function testGetHolidaysThrowsExceptionForYearWithoutHolidays()
    {
        $year = '2022';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Holidays $year not found");



        $storageMock = $this->createMock(FileStorageService::class);
        $storageMock->method('isFileValid')->willReturn(true);
        $storageMock->method('getDataFromFile')->willReturn([]);

        $fetcherMock = $this->createMock(HolidayDataFetcherInterface::class);

        $service = new HolidayManagerService($storageMock, $fetcherMock);

        $service->getHolidays($year);
    }
}
