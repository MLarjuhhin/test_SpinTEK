<?php

namespace Config;

class Config
{
    private static $instance = null;
    private $settings = [];

    private function __construct()
    {
        $this->settings = require __DIR__ . '/../../config/config.php';
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key)
    {
        return $this->settings[$key] ?? false;
    }
}
