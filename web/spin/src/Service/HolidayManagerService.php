<?php

namespace Service;

use Exception;
use Interface\HolidayDataFetcherInterface;
use Service\FileStorageService;

class HolidayManagerService
{
    /**
     * @var FileStorageService An object for working with file storage of holiday data.
     */
    private $storage;

    /**
     * @var HolidayDataFetcherInterface Object for loading data about holidays.
     */
    private $fetcher;

    /**
     *
     * Creates an instance of HolidayManager.
     *
     * @param FileStorageService $storage Object for working with local holiday data storage.
     * @param HolidayDataFetcherInterface $fetcher An object to fetch holiday data from an external source.
     *
     */
    public function __construct(FileStorageService $storage, HolidayDataFetcherInterface $fetcher)
    {
        $this->storage = $storage;
        $this->fetcher = $fetcher;
    }

    /**
     * Gets a list of holidays for the specified year.
     *
     * This method checks whether the holiday data in local storage is up to date. If the data is out of date or missing,
     * method downloads fresh data using an https://xn--riigiphad-v9a.ee/, filters it by event type and saves it to a file.
     * Then extracts holiday data from the file and filters it by the specified year.
     * If there is no data on holidays for a given year, an exception is thrown.
     *
     * @param string|int $year The year for which to get a list of holidays.
     * @return array An array of holidays for the specified year.
     * @throws Exception If no holidays are found for the specified year.
     */

    public function getHolidays($year): array
    {

        if (!$this->storage->isFileValid()) {

            $data = $this->fetcher->fetchData();

            $new_data = $this->storage->filterDataByKindId($data);
            $this->storage->saveDataToFile($new_data);
        } else {
            $data = $this->storage->getDataFromFile();
        }


        $filteredHolidays = $this->filterHolidaysByYear($data);

        if (empty($filteredHolidays) || !isset($filteredHolidays[$year])) {
            throw new Exception("Holidays $year not found");
        }
        return $filteredHolidays;
    }

    /**
     * Groups holiday dates by year.
     *
     * @param array $holidays An array of holidays with dates in the format ['date' => 'YYYY-MM-DD']
     * @return array Grouped holiday dates by year
     */
    private function filterHolidaysByYear(array $holidays): array
    {
        $ret = [];
        foreach ($holidays as $v) {
            $ret[date('Y', strtotime($v['date']))][] = $v['date'];
        }
        return $ret;
    }
}