<?php

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Service\FileStorageService;

class FileStorageTest extends TestCase
{


    public function testIsFileValidReturnsFalseWhenFileDoesNotExist()
    {
        $service = new FileStorageService();
        $reflection = new \ReflectionClass($service);
        $filePath = $reflection->getProperty('filePath');
        $filePath->setValue($service, 'asdasd/file.json');

        $this->assertFalse($service->isFileValid(), "File is valid, but not exists");
    }

    public function testIsFileValidReturnsTrueForValidFile()
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test');
        if ($tempFilePath === false) {
            throw new \Exception("Failed to create tmp file.");
        }

        touch($tempFilePath);

        $service = new FileStorageService(1);
        $reflection = new \ReflectionClass($service);

        $filePath = $reflection->getProperty('filePath');
        $filePath->setValue($service, $tempFilePath);

        try {
            $this->assertTrue($service->isFileValid(), "File is valid, and exits");
        } finally {
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }
    }

    public function testGetDataFromFileThrowsExceptionWhenFileNotFound()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("File not found.");

        $service = new FileStorageService();
        $reflection = new \ReflectionClass($service);
        $filePath = $reflection->getProperty('filePath');
        $filePath->setValue($service, 'asdasd/file.json');

        $service->getDataFromFile();
    }

    public function testGetDataFromFileReturnsDataForValidFile()
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test');
        if ($tempFilePath === false) {
            throw new \Exception("Failed to create tmp file.");
        }

        $testData = ['key' => 'value'];
        file_put_contents($tempFilePath, json_encode($testData));

        $service = new FileStorageService();
        $reflection = new \ReflectionClass($service);
        $filePath = $reflection->getProperty('filePath');
        $filePath->setValue($service, $tempFilePath);

        $data = $service->getDataFromFile();
        $this->assertIsArray($data, "Data is not array.");
        $this->assertEquals($testData, $data, "Data does not match.");

        unlink($tempFilePath);
    }

    public function testSaveDataToFileThrowsExceptionForEmptyData()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("No data to save.");

        $service = new FileStorageService();
        $service->saveDataToFile([]);
    }


    public function testSaveDataToFileSavesDataCorrectly()
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test');
        if ($tempFilePath === false) {
            throw new \Exception("Failed to create tmp file.");
        }

        $service = new FileStorageService();
        $reflection = new \ReflectionClass($service);
        $filePath = $reflection->getProperty('filePath');
        $filePath->setValue($service, $tempFilePath);

        $testData = ['key' => 'value'];
        $service->saveDataToFile($testData);

        $fileContents = file_get_contents($tempFilePath);
        if ($fileContents === false) {
            throw new \Exception("Error reading file.");
        }

        $decodedData = json_decode($fileContents, true);
        $this->assertEquals($testData, $decodedData, "Data does not match.");

        unlink($tempFilePath);
    }

    public function testFilterDataByKindIdFiltersCorrectly()
    {
        $data = [
            ['name' => 'Item 1', 'kind_id' => 1],
            ['name' => 'Item 2', 'kind_id' => 2],
            ['name' => 'Item 3', 'kind_id' => 3],
        ];

        $service = new FileStorageService();
        $filteredData = $service->filterDataByKindId($data);

        $this->assertCount(2, $filteredData, "The data should contain only 2 items.");
        $this->assertContainsOnly('array', $filteredData, "Returned data not array");
        $this->assertEquals([0 => $data[0], 1 => $data[1]], array_values($filteredData), "Data does not match");

    }
}
