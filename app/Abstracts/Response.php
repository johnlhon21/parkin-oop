<?php


namespace App\Abstracts;


class Response
{
    protected $response;

    /**
     * @param array $data
     * @param $status
     * @param $message
     * @return object
     */
    public function with(array $data = [], $message = null, $status = 200)
    {
        $this->response = (object) $this->response;
        $this->response->status = $status;
        $this->response->message = $message;
        $this->response->data = [];

        foreach ($data as $key => $value) {
            $this->response->data[$key] = $value;
        }

        return $this->response;
    }
}
