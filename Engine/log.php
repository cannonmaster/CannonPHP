<?php

namespace Engine;

class Log
{
    private string $log_path;

    /**
     * Log constructor.
     *
     * @param string $log_path The path to the log file.
     */
    public function __construct(string $log_path)
    {
        $this->log_path = $log_path;
    }

    /**
     * Write a log message to the log file.
     *
     * @param string $message The log message to be written.
     *
     * @return void
     */
    public function logging(string $message): void
    {
        $log = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL;
        file_put_contents($this->log_path, $log, FILE_APPEND | LOCK_EX);
    }
}
