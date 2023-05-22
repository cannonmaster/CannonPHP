<?php

namespace Engine;

class Request
{
    public array $get = [];
    public array $post = [];
    public array $cookie = [];
    public array $files = [];
    public array $server = [];

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->get = $this->clear($_GET);
        $this->post = $this->clear($_POST);
        $this->files = $this->clear($_FILES);
        $this->server = $this->clear($_SERVER);
        $this->cookie = $this->clear($_COOKIE);
    }

    /**
     * Clear the input data by removing potential security risks and sanitizing the values.
     *
     * @param mixed $data The input data to be cleared.
     *
     * @return mixed The cleared input data.
     */
    private function clear($data): mixed
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
