<?php

use Controller\HolidayController;
use PHPUnit\Framework\TestCase;

class HolidayControllerTest extends TestCase
{


    public function testGetHolidaysSuccess()
    {
        $year = 2025;
        $holidayController = new HolidayController();

        $result = $holidayController->getHolidays($year);

        $this->assertArrayHasKey('data', $result,"'data' is not contained in  array");
    }


    public function testGetHolidaysException()
    {
        $year = 2020;
        $expectedResult = ['error' => 'Service is temporarily unavailable. Try later.'];

        $holidayController = new HolidayController();

        $result = $holidayController->getHolidays($year);

        $this->assertEquals(
            $expectedResult,
            $result,
            "the data does not match. Expected: ".json_encode($expectedResult)
        );
    }
}
