<?php

namespace App;

/**
 * Class Config
 *
 * @package App
 */
class Config
{
    /**
     * @var string The database username.
     */
    const username = 'root';

    /**
     * @var string The database password.
     */
    const password = 'root';

    /**
     * @var string The database name.
     */
    const dbname = 'test';

    /**
     * @var int The database port.
     */
    const port = 8889;

    /**
     * @var string The database host.
     */
    const host = 'localhost';

    /**
     * @var string The default language.
     */
    const language = 'en';

    /**
     * @var string The database engine.
     */
    const db_engine = 'pdo';

    /**
     * @var bool Whether to auto-start the database connection.
     */
    const db_autostart = true;

    /**
     * @var string The command builder for database queries using orm.
     */
    const db_command_builder = 'MysqlCommandBuilder';

    /**
     * @var array The supported languages.
     */
    const support_languages = ['en'];

    /**
     * @var string The default theme.
     */
    const theme = 'default';

    /**
     * @var array The allowed file upload types.
     */
    const file_upload_allow_type = [
        'png',
        'jpg',
        'txt'
    ];

    /**
     * @var int The maximum file upload size.
     */
    const file_upload_max_size = 5680000;

    /**
     * @var string The cache engine.
     */
    const cache_engine = 'redis';

    /**
     * @var string The cache hostname.
     */
    const cache_hostname = 'localhost';

    /**
     * @var string The cache password.
     */
    const cache_password = '';

    /**
     * @var string The cache schema.
     */
    const cache_schema = 'tcp';

    /**
     * @var int The cache port.
     */
    const cache_port = 6379;

    /**
     * @var int The cache expiration time in seconds.
     */
    const cache_expire = 3600;

    /**
     * @var array The cache SSL options.
     */
    const cache_ssl = [];

    /**
     * @var string The cache persistence mode.
     */
    const cache_persistent = '1';

    /**
     * @var string The folder path for log files.
     */
    const log_folder = __DIR__ . '/../log/';

    /**
     * @var string The log filename.
     */
    const log_filename = 'custom.txt';

    /**
     * @var string The full path of the log file.
     */
    const log_path = self::log_folder . self::log_filename;

    /**
     * @var bool Whether to show error messages.
     */
    const show_error = true;

    /**
     * @var string The path to the routes file.
     */
    const routes = __DIR__ . '/Routes.php';

    /**
     * @var bool Whether to auto-start the session.
     */
    const session_autostart = true;

    /**
     * @var string The session engine.
     */
    const session_engine = 'redis';

    /**
     * @var string The session database table.
     */
    const session_db_table = 'cannon_session';

    /**
     * @var string The session Redis host.
     */
    const session_redis_host = 'localhost';

    /**
     * @var int The session Redis port.
     */
    const session_redis_port = 6379;
    /**
     * @var string The name of the session.
     */
    const session_name = 'cannon_session';

    /**
     * @var string The path for session cookies.
     */
    const session_path = '/';

    /**
     * @var bool Whether to use HTTP-only session cookies.
     */
    const session_http_only = true;

    /**
     * @var string The session domain.
     */
    const session_domain = '';

    /**
     * @var string The SameSite attribute for session cookies.
     */
    const session_samesite = 'Strict';

    /**
     * @var int The session expiration time in seconds.
     */
    const session_expire = 60000;

    /**
     * @var int The compression level for response output.
     */
    const compressLevel = 1;

    /**
     * @var array The headers to be sent with each response.
     */
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
