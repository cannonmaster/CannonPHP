<?php

namespace Engine;

class Request
{
    public $get = [];
    public $post = [];
    public $cookie = [];
    public $files = [];
    public $server = [];

    public function __construct()
    {
        $this->get = $this->clear($_GET);
        $this->post = $this->clear($_POST);
        $this->files = $this->clear($_FILES);
        $this->server = $this->clear($_SERVER);
        $this->cookie = $this->clear($_COOKIE);
    }

    private function clear($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                unset($data[$key]);
                $data[$this->clear($key)] = $this->clear($val);
            }
        } else {
            $data = trim(htmlspecialchars($data, ENT_COMPAT, 'UTF-8'));
        }

        return $data;
    }
}
