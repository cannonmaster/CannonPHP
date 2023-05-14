<?php

namespace App\Session;

interface SessionAdapterInterface
{
    public function read(string $sessionId): array;
    public function write(string $sessionId, array $data): void;
    public function destroy(string $sessionId): void;
    public function gc(string $maxLifetime): void;
}
