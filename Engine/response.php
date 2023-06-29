<?php

namespace Engine;

class Response
{
    private $output;
    private array $headers = [];
    private $di;
    private $compressLevel = 0;
    private $encoding = null;

    /**
     * Response constructor.
     *
     * @param \Engine\Di $di The dependency injection container.
     */
    public function __construct(\Engine\Di $di)
    {
        $this->di = $di;
    }

    /**
     * Add a header to the response.
     *
     * @param string $header The header string to add.
     *
     * @return self
     */
    public function addHeader(string $header): self
    {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * Get the headers of the response.
     *
     * @return array The response headers.
     */
    public function getHeader(): array
    {
        return $this->headers;
    }

    /**
     * Get the output of the response.
     *
     * @return mixed The response output.
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Compress the response data based on the compression level and encoding.
     *
     * @param int    $level The compression level.
     * @param string|array $data  The data to compress.
     *
     * @return string The compressed data.
     */
    private function compress(int $level, $data): string
    {
        $level = $level ?: $this->compressLevel;
        $data = is_array($data) ? json_encode($data) : $data;
        if ($level < -1 || $level > 9) {
            return $data;
        }
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            $this->encoding = 'gzip';
        } elseif (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
            $this->encoding = 'x-gzip';
        }

        if (!isset($this->encoding)) {
            return $data;
        }

        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            return $data;
        }

        if (headers_sent() || connection_status()) {
            return $data;
        }
        // $compressed = gzencode($data, $level);
        // $this->addHeader('Content-Encoding: ' . $this->encoding);
        // $this->addHeader('Content-Length: ' . strlen($compressed));

        // return $compressed;
        return $data;
    }

    /**
     * Set the compression level for the response.
     *
     * @param int $compressLevel The compression level to set.
     */
    public function setCompressionLevel(int $compressLevel): void
    {
        $this->compressLevel  = $compressLevel;
    }

    /**
     * Set the output of the response.
     *
     * @param mixed $output The response output.
     *
     * @return self
     */
    public function setOutput($output): self
    {
        $this->output = $output;
        return $this;
    }

    public function output(): void
    {
        if ($this->output) {
            $this->output = $this->compressLevel ? $this->compress($this->compressLevel, $this->output) : $this->output;
        }
        $current_route = $this->currentRoute();
        if (!headers_sent()) {
            foreach ($this->headers as $header) {
                header($header, true);
            }
        }
        echo $this->output;
        $this->afterActionHook($current_route);
        $this->afterControllerHook($current_route);
        // After action hook
    }

    /**
     * Get the current route object from the dependency injection container.
     *
     * @return mixed The current route object.
     */
    private function currentRoute()
    {
        return $this->di->get('currentRoute');
    }

    /**
     * Call the afterAction method on the current route object.
     *
     * @param mixed $current_object The current route object.
     */
    public function afterActionHook($current_object): void
    {

        $current_object->afterAction();
    }

    /**
     * Call the afterController method on the current route object.
     *
     * @param mixed $current_object The current route object.
     */
    public function afterControllerHook($current_object): void
    {
        $current_object->afterController();
    }

    public function redirect($url)
    {
        header('Location: ' . $url, true, 302);
        exit();
    }

    public function json(array $responseData)
    {
        $this->addHeader('Content-Type: application/json');

        // Convert the data to JSON format
        $jsonData = json_encode($responseData);

        // Output the JSON response
        echo $jsonData;
    }
}
