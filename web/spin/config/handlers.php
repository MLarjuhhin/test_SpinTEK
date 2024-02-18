<?php
set_exception_handler(function ($exception) {
    error_log($exception->getMessage());
    if (!headers_sent()) {
        header('Content-Type: application/json', true, 500);
    }
    echo json_encode(['error' => 'Service is temporarily unavailable. Try later.']);
    exit;
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("$errstr in $errfile on line $errline");
    if (!headers_sent()) {
        header('Content-Type: application/json', true, 500);
    }
    echo json_encode(['error' => 'Service is temporarily unavailable. Try later.']);
    exit;
});