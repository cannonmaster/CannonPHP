<?php

namespace Core;

class Error
{
    protected $config;

    /**
     * Error constructor.
     *
     * @param mixed $config The configuration object.
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Error handler function for handling PHP errors.
     *
     * @param int    $level   The error level.
     * @param string $message The error message.
     * @param string $file    The file in which the error occurred.
     * @param int    $line    The line number where the error occurred.
     *
     * @throws \ErrorException
     */
    public static function errorHandler(int $level, string $message, string $file, int $line): void
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler function for handling uncaught exceptions.
     *
     * @param \Throwable $exception The uncaught exception.
     */
    public static function exceptionHandler(\Throwable $exception): void
    {
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);
        if (\App\Config::show_error) {
            echo "<h1>Fatal Error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p> ";
            echo "<p>Message: '" . $exception->getMessage() . "' </p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
            $log = dirname(__DIR__) . '/log/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);
            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\n Stack trace: " . $exception->getTraceAsString();
            $message .= "\n Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine();
            error_log($message);
            View::renderTemplate("$code.html");
        }
    }
}
