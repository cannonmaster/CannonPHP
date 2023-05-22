<?php

namespace Engine;

class Session
{
    protected \App\Session\SessionAdapterInterface $adapter;
    protected ?string $session_id = null;
    public array $data = [];

    /**
     * Session constructor.
     *
     * @param string   $session_engine The session engine class name.
     * @param Di       $di             The dependency injection container.
     *
     * @throws \Exception If the session adapter class cannot be loaded.
     */
    public function __construct(string $session_engine, \Engine\Di $di)
    {
        $session_engine = "App\\Session\\$session_engine";
        if (class_exists($session_engine)) {
            $this->adapter = new $session_engine($di);

            register_shutdown_function([&$this, 'close']);
            register_shutdown_function([&$this, 'gc']);
        } else {
            throw new \Exception('Error: Could not load session adapter ' . $session_engine . ' session!');
        }
    }

    /**
     * Get the session ID.
     *
     * @return string|null The session ID.
     */
    public function getId(): ?string
    {
        return $this->session_id;
    }

    /**
     * Start the session.
     *
     * @param string|null $session_id The session ID to start. If null, a new session ID will be generated.
     *
     * @return string|null The started session ID.
     *
     * @throws \Exception If an invalid session ID is provided.
     */
    public function start(?string $session_id = null): ?string
    {
        // Prevent the start function from being called multiple times
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Set the session name
            session_name(\App\Config::session_name);
            if ($session_id !== null) {
                session_id($session_id);
            }
            // Start the session
            session_start();

            if ($session_id === null) {
                $session_id = session_id();
            }

            // Check if the session ID is valid
            if (!preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $session_id)) {
                throw new \Exception('Error: Invalid session ID!');
            }

            $this->session_id = $session_id;
        }

        $this->data = $this->adapter->read($session_id);

        return $session_id;
    }

    /**
     * Close the session.
     */
    public function close(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->adapter->write($this->session_id, $this->data);
            session_write_close();
        }
    }

    /**
     * Perform garbage collection on the session.
     */
    public function gc(): void
    {
        $this->adapter->gc($this->session_id);
    }

    /**
     * Destroy the session.
     */
    public function destroy(): void
    {
        $this->data = [];
        $this->adapter->destroy($this->session_id);

        // Clear the session data
        session_unset();

        // Destroy the session
        session_destroy();

        $this->session_id = null;
    }
}
