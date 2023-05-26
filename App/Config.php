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

    const support_languages = ['en'];

    const theme = 'default';

    const file_upload_allow_type = [
        'png',
        'jpg', 'txt'
    ];
    const file_upload_max_size = 5680000;

    const cache_engine = 'redis';
    const cache_hostname = 'localhost';
    const cache_password = '';
    const cache_schema = 'tcp';
    const cache_port = 6379;
    const cache_expire = 3600;
    const cache_ssl = [];
    const cache_persistent = '1';
    const log_folder = __DIR__ . '/../log/';
    const log_filename = 'custom.txt';
    // const cache_options = array();
    const log_path = self::log_folder . self::log_filename;
    const show_error = true;
    const routes = __DIR__ . '/Routes.php';

    const session_autostart = true;
    const session_engine = 'redis';
    const session_db_table = 'cannon_session';
    const session_redis_host = 'localhost';
    const session_redis_port = 6379;
    const session_name = 'cannon_session';
    const session_path = '/';
    const session_http_only = true;
    const session_domain = '';
    const session_samesite = 'Strict';
    const session_expire = 60000;

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
