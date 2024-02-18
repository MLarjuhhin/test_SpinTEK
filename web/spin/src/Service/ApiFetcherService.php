<?php

namespace Service;

use Config\Config;
use Exception;
use Interface\HolidayDataFetcherInterface;

class ApiFetcherService implements HolidayDataFetcherInterface
{
    private $url;

    public function __construct()
    {
       $this->url = Config::getInstance()->get('apiUrl');

    }

    public function fetchData(): array
    {
        if (empty($this->url)) {
            throw new Exception('API URL is not set.');
        }

        $json = $this->getFileContents($this->url);
        if ($json === false) {
            throw new Exception("Unable to retrieve data from API URL: {$this->url}");
        }

        return json_decode($json, true);
    }


    protected function getFileContents($url)
    {
        return file_get_contents($url);
    }
}