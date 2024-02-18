<?php

namespace Service;

use Config\Config;
use Exception;
use Interface\HolidayDataFetcherInterface;

class ApiFetcherService implements HolidayDataFetcherInterface
{
    /**
     * @var false|mixed External API URL for data retrieval.
     */
    private $url;

    public function __construct()
    {
        $this->url = Config::getInstance()->get('apiUrl');

    }

    /**
     * Method for retrieving data from an API.
     *
     * @return array Returns an array of data received from the API.
     * @throws Exception If the API URL is not set or if the data could not be retrieved.
     */
    public function fetchData(): array
    {
        if (empty($this->url)) {
            throw new Exception('API URL is not set.');
        }

        $json = $this->getFileContents($this->url);
        if ($json === false) {
            throw new Exception("Unable to retrieve data from API URL");
        }

        return json_decode($json, true);
    }

    /**
     * Protected method for retrieving content
     *
     *
     * @param  string  $url  URL from which to retrieve the content.
     * @return false|string Contents in string format or false in case of error.
     */
    protected function getFileContents($url)
    {
        return file_get_contents($url);
    }
}