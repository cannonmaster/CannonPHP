<?php

namespace Engine;

class Profile
{
    private $mem_start_usage;
    private $start_time;
    public $functions_call;
    public \Engine\Di $di;

    /**
     * Profile constructor.
     *
     * @param \Engine\Di $di The dependency injection container.
     */
    public function __construct(\Engine\Di $di)
    {
        $this->di = $di;
    }

    /**
     * Start profiling.
     *
     * @return void
     */
    public function start(): void
    {
        $this->start_time = microtime(true);
        $this->mem_start_usage = memory_get_usage(true);
        $this->addFunctionCall(__METHOD__);
    }

    /**
     * End profiling and print the results.
     *
     * @return void
     */
    public function end(): void
    {
        $execution_time = microtime(true) - $this->start_time;
        $memory_usage = memory_get_peak_usage(true)  - $this->mem_start_usage;
        $this->addFunctionCall(__METHOD__);

        $this->printProfile($execution_time, $memory_usage);
    }

    /**
     * Add a function call to the list of profiled function calls.
     *
     * @param string $functionName The name of the function being called.
     *
     * @return void
     */
    public function addFunctionCall($functionName): void
    {
        $this->functions_call[] = $functionName;
    }

    /**
     * Print the profiling results.
     *
     * @param float $execution_time The execution time in seconds.
     * @param int $memory_usage The memory usage in bytes.
     *
     * @return void
     */
    private function printProfile($execution_time, $memory_usage): void
    {
        echo "Execution Time: {$execution_time} seconds" . PHP_EOL;
        echo "Memory Usage: {$memory_usage} bytes" . PHP_EOL;
        echo "Function calls:" . PHP_EOL;
        foreach ($this->functions_call as $function_call) {
            echo $function_call . PHP_EOL;
        }
    }
}
