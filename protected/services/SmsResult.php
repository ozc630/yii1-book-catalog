<?php

class SmsResult
{
    public $success;
    public $response;
    public $error;

    public function __construct(bool $success, $response = null, ?string $error = null)
    {
        $this->success = $success;
        $this->response = $response;
        $this->error = $error;
    }
}
