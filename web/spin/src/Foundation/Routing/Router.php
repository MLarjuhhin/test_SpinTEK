<?php


namespace Foundation\Routing;

use Controller\HolidayController;
use Foundation\Http\Response;

class Router
{
    // Adding a helper method for an error response
    private function errorResponse($statusCode, $message) {
        return new Response($statusCode, [], $message);
    }

    public function handle(string $uri,string $method): Response
    {
        $path = substr(parse_url($uri, PHP_URL_PATH), 1);
        $segments = explode('/', $path);

        $url = $segments[0] ?? '';
        $year = $segments[1] ?? date("Y");

        // Year validity check
        if (!preg_match('/^\d{4}$/', $year)) {
            return $this->errorResponse(400, "Invalid Year");
        }

        switch ($url) {
            case 'schedule':
                if ($method !== 'GET') {
                    return $this->errorResponse(405, 'Method Not Allowed');
                }
                return $this->handleSchedule($year);
            default:
                return $this->errorResponse(404, 'Not Found');
        }
    }
    /**
     * Обрабатывает запрос на получение списка праздников за указанный год.

     *
     * @param int $yearThe year for which the list of holidays is being requested.
     * @return Response Returns a Response object data or an error message.
     * @throws \Exception Exception
     */
    private function handleSchedule(int $year): Response
    {
        try {
            $controller = new HolidayController();
            $data = $controller->getHolidays($year);

            if (isset($data['data'])) {
                return new Response(200, $data['data']);
            } else {
                $errorMsg = $data['error'] ?? "Service is temporarily unavailable. Try later.";
                return $this->errorResponse(500, $errorMsg);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->errorResponse(500, "Service is temporarily unavailable. Try later.");
        }
    }
}
