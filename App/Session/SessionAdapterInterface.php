<?php

namespace App\Session;

/**
 * The SessionAdapterInterface defines the methods that a session adapter must implement.
 */
interface SessionAdapterInterface
{
    /**
     * Read session data for the specified session ID.
     *
     * @param string $sessionId The session ID.
     * @return array The session data as an associative array.
     */
    public function read(string $sessionId): array;

    /**
     * Write session data for the specified session ID.
     *
     * @param string $sessionId The session ID.
     * @param array $data The session data as an associative array.
     * @return void
     */
    public function write(string $sessionId, array $data): bool;

    /**
     * Destroy the session data for the specified session ID.
     *
     * @param string $sessionId The session ID.
     * @return void
     */
    public function destroy(string $sessionId): bool;

    /**
     * Perform garbage collection on expired session data.
     *
     * @param string $maxLifetime The maximum lifetime of a session (in seconds).
     * @return void
     */
    public function gc(string $maxLifetime): bool;
}
