<?php
namespace App\Http;

class Response
{
    public $code;
    public $data;

    public function __toString()
    {
        return json_encode(['code' => $this->code, 'data' => $this->data]);
    }
}