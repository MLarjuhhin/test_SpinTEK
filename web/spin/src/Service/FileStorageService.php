<?php

namespace Service;

use Config\Config;
use Exception;

class FileStorageService
{
    /**
     * @var string Path to the file with holiday data
     */
    private $filePath = __DIR__.'/../../storage/holidays.json';
    /**
     * @var int The number of days after which a file is considered obsolete
     */
    private $expiryTime;

    /**
     * Constructor for the FileStorage class.
     *
     * @param  string  $filePath  Path to the file.
     * @param  int  $expiryDays  File lifetime in days. Default is 30 days
     */

    public function __construct()
    {
        $expiryDays = Config::getInstance()->get('fileExpiryDays')?:30;
        $this->expiryTime = $expiryDays * 24 * 60 * 60;
    }

    /**
     * Checks the validity of the file based on the last modification date
     *
     * @return bool Returns true if the file is valid, false otherwise.
     */

    public function isFileValid(): bool
    {
        return file_exists($this->filePath) && (time() - filemtime($this->filePath) < $this->expiryTime);
    }

    /**
     * Reads data from a file and returns it as an array.
     *
     * @return array An array of data read from the file.
     * @throws Exception If the file is not found, cannot be read, or contains invalid JSON.
     */

    public function getDataFromFile(): array
    {
        if (!file_exists($this->filePath)) {
            throw new Exception("File not found.");
        }

        $fileContents = file_get_contents($this->filePath);
        if ($fileContents === false) {
            throw new Exception("Error reading file .");
        }

        $data = json_decode($fileContents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding JSON from file ");
        }

        return $data;
    }

    /**
     * Saves data to a file.
     *
     * @param  mixed  $data  Data to save to file. The data must be in the format
     * supported for JSON encoding.
     * @throws Exception If the data is empty or an error occurred while saving to the file.
     */
    public function saveDataToFile($data): void
    {
        if (empty($data)) {
            throw new Exception("No data to save.");
        }
        if (empty($this->filePath)) {
            throw new Exception("File path is not set.");
        }

        $result = file_put_contents($this->filePath, json_encode($data));
        if ($result === false) {
            throw new Exception("Failed to save data to file .");
        }
    }


    /**
     * Filters data by the condition kind_id = 1 or 2.
     *
     * @param  array  $data  Array of data to filter.
     * @return array Filtered data.
     */
    public function filterDataByKindId($data): array
    {
        return array_filter($data, function ($item) {
            return isset($item['kind_id']) && ($item['kind_id'] == 1 || $item['kind_id'] == 2);
        });
    }

}