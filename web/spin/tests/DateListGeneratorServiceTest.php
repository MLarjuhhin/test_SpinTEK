<?php


use PHPUnit\Framework\TestCase;
use Service\ApiFetcherService;
use Service\DateListGeneratorService;
use Service\FileStorageService;
use Service\HolidayManagerService;

class DateListGeneratorServiceTest extends TestCase
{
    private $holidayManagerService;

    protected function setUp(): void
    {
        $this->holidayManagerService = $this->createMock(HolidayManagerService::class);

    }

    public function testGenerateListReturnsCorrectStructure()
    {
        $this->holidayManagerService->method('getHolidays')->willReturn([]);
        $generator = new DateListGeneratorService(2025, $this->holidayManagerService);
        $result = $generator->generateList();

        $this->assertIsArray($result,'We return not an array');
        $this->assertCount(12, $result,"Array length is not equal to 12");

        $this->assertArrayHasKey('pay_day', $result[1],"The array does not contain the 'pay_day' key.");
        $this->assertArrayHasKey('notification_day', $result[1],"The array does not contain the 'notification_day' key.");
    }
    public function testConstructorSetsInitialValuesCorrectly2025Year()
    {
        $holidayManagerMock = $this->createMock(HolidayManagerService::class);
        $holidayManagerMock->method('getHolidays')->willReturn([
            '2025' => [
                '2025-01-01',
                '2025-02-24',
                '2025-04-18',
                '2025-04-20',
                '2025-05-01',
                '2025-06-08',
                '2025-06-23',
                '2025-06-24',
                '2025-08-20',
                '2025-12-24',
                '2025-12-25',
                '2025-12-26',
            ],
        ]);
        $generator = new DateListGeneratorService(2025, $holidayManagerMock,23);
        $generateList = $generator->generateList();
        $expectedDates = [
            1 => ['pay_day' => '2025-01-23', 'notification_day' => '2025-01-20'],
            2 => ['pay_day' => '2025-02-21', 'notification_day' => '2025-02-18'],
            3 => ['pay_day' => '2025-03-21', 'notification_day' => '2025-03-18'],
            4 => ['pay_day' => '2025-04-23', 'notification_day' => '2025-04-17'],
            5 => ['pay_day' => '2025-05-23', 'notification_day' => '2025-05-20'],
            6 => ['pay_day' => '2025-06-20', 'notification_day' => '2025-06-17'],
            7 => ['pay_day' => '2025-07-23', 'notification_day' => '2025-07-18'],
            8 => ['pay_day' => '2025-08-22', 'notification_day' => '2025-08-18'],
            9 => ['pay_day' => '2025-09-23', 'notification_day' => '2025-09-18'],
            10 => ['pay_day' => '2025-10-23', 'notification_day' => '2025-10-20'],
            11 => ['pay_day' => '2025-11-21', 'notification_day' => '2025-11-18'],
            12 => ['pay_day' => '2025-12-23', 'notification_day' => '2025-12-18'],

        ];


        foreach ($expectedDates as $month => $dates) {
            $this->assertEquals(
                $dates['pay_day'],
                date("Y-m-d",strtotime($generateList[$month]['pay_day'])),
                "The pay_day date for  month {$month} is not  expected. Expected: {$dates['pay_day']}, received: " . date("Y-m-d", strtotime($generateList[$month]['pay_day'])));

            $this->assertEquals(
                $dates['notification_day'],
                date("Y-m-d",strtotime($generateList[$month]['notification_day'])),
                "The notification_day date for  month {$month} is not  expected. Expected: {$dates['notification_day']}, received: " . date("Y-m-d", strtotime($generateList[$month]['notification_day']))
            );



        }

    }


}
