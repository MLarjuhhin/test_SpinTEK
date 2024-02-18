<?php

namespace Foundation\Http;

class Response
{
    protected $statusCode;
    protected $body;
    protected $msg;

    public function __construct(int $statusCode, $body, string $msg = '')
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->msg = $msg;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMsg(): string
    {
        return $this->msg;
    }

    public function getBody(): string
    {
        $status = $this->isSuccess() ? 'success' : 'error';
        $response = ['status' => $status];

        if ($this->isSuccess()) {
            $response['data'] = $this->body;
        }else{
            $response['msg'] = $this->msg;
        }

        return json_encode($response);
    }

    protected function isSuccess()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }


}
