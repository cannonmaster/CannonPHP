<?php

namespace Engine;

class Response
{
    private $output;
    private array $headers = [];
    private $di;
    private $compressLevel = 0;
    private $encoding  = null;
    public function __construct(\Engine\Di $di)
    {
        $this->di = $di;
    }

    public function addHeader(string $header)
    {
        $this->headers[] = $header;
        return $this;
    }

    public function getHeader()
    {
        return $this->headers;
    }

    public function getOutput()
    {
        return $this->output;
    }

    private function compress(int $level, string $data)
    {

        $level = $level ?: $this->compressLevel;
        if ($level < -1 || $level > 9) {
            return $data;
        }
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            $this->encoding = 'gzip';
        } else if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
            $this->encoding = 'x-gzip';
        }

        if (!isset($this->encoding)) {
            return $data;
        }

        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            return $data;
        }

        if (headers_sent()) {
            return $data;
        }

        if (connection_status()) {
            return $data;
        }
        $compressed = gzencode($data, $level);
        $this->addHeader('Content-Encoding: ' . $this->encoding);
        $this->addHeader('Content-Length: ' . strlen($compressed));

        return $compressed;
    }

    public function setCompressionLevel(int $compressLevel)
    {
        $this->compressLevel  = $compressLevel;
    }

    public function setoutput($output)
    {
        $this->output = $output;
        return $this;
        // $this->output();
    }

    public function output()
    {
        if ($this->output) {
            $output = $this->compressLevel ? $this->compress($this->compressLevel, $this->output) : $this->output;

            if (!headers_sent()) {
                foreach ($this->headers as $header) {
                    header($header, true);
                }
            }
            echo $output;
            // echo $this->output;

            // after action hook
            $current_route = $this->currentRoute();

            $this->afterActionHook($current_route);
            $this->afterControllerHook($current_route);
        }
    }

    private function currentRoute()
    {
        return $this->di->get('currentRoute');
    }

    public function afterActionHook($current_object)
    {
        $current_object->afterAction();
    }
    public function afterControllerHook($current_object)
    {
        $current_object->after();
    }
}
