<?php

namespace Controller;


use Service\ApiFetcherService;
use Service\DateListGeneratorService;
use Service\FileStorageService;
use Service\HolidayManagerService;

class HolidayController
{
    /**
     * @var HolidayManagerService  Service for managing holiday data.
     */
    private $holidayManager;

    public function __construct()
    {
        $storage = new FileStorageService();
        $fetcher = new ApiFetcherService();
        $this->holidayManager = new HolidayManagerService($storage, $fetcher);
    }

    /**
     * Gets a list of holidays for a given year.
     *
     * @param  int  $year The year for which holiday information is being requested.
     * @return array|string[] An array containing holiday data or an error message.
     */
    public function getHolidays(int $year): array
    {

        try {
            $DateListGenerator = $this->createDateListGeneratorService($year);
            $result = $DateListGenerator->generateList();

            return ['data' => $result];

        } catch (\Exception $e) {
            error_log($e->getMessage());
            return ['error' => 'Service is temporarily unavailable. Try later.'];
        }
    }

    /**
     * Creates and returns an instance of DateListGeneratorService for the given year
     * @param  int  $year The year for which it is necessary to create a service for generating a list of holidays.
     * @return DateListGeneratorService Service for generating a list of holidays.
     */
    protected function createDateListGeneratorService(int $year): DateListGeneratorService
    {
        return new DateListGeneratorService($year, $this->holidayManager);
    }

}
