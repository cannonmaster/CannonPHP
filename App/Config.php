<?php

namespace App;

class Config
{
    const username = 'root';
    const password = 'root';
    const dbname = 'cannon';
    const port = 8889;
    const host = 'localhost';
    const language = 'en';

    const db_engine = 'pdo';
    const db_autostart = true;

    const cache_engine = 'redis';
    const cache_hostname = 'localhost';
    const cache_password = '';
    const cache_schema = 'tcp';
    const cache_port = '6379';
    const cache_ssl = [];
    const cache_persistent = '1';
    // const cache_options = array();
    const show_error = true;
    const routes = __DIR__ . '/Routes.php';

    const session_autostart = true;
    const session_engine = 'redis';
    const session_name = 'cannon_session';
    const session_path = '/';
    const session_http_only = true;
    const session_domain = '';
    const session_samesite = 'Strict';

    const compressLevel = 1;
    const header = [
        'Access-Control-Allow-Origin: *',
        'Access-Control-Allow-Credentials: true',
        'Access-Control-Max-Age: 1000',
        'Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding',
        'Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE',
        'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
        'Pragma: no-cache'
    ];
}
