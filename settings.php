<?php

/**
 * README
 * This configuration file is intended to be used as the main script for the PHP Telegram Bot Manager.
 * Uncommented parameters must be filled
 *
 * For the full list of options, go to:
 * https://github.com/php-telegram-bot/telegram-bot-manager#set-extra-bot-parameters
 */

use Longman\TelegramBot\TelegramLog;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define ('BASE_PATH', __DIR__);
define ('TEMPLATE_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'Templates');
define ('SYSTEM_COMMANDS_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'Commands');
define ('USER_COMMANDS_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'UserCommands');
define ('ADMIN_COMMANDS_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'AdminCommands');
define ('UPLOAD_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'Upload');
define ('DOWNLOAD_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'Download');
define ('LOGS_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'Logs');
define ('SITE_NAME', 'tools.ua');
define ('SITE_NEWS_URL', 'https://tools.ua/');

require_once BASE_PATH . DIRECTORY_SEPARATOR .  'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

$botUsername = getenv('BOT_USERNAME');

$botSettings = [
    // Add you bot's API key and name
    'api_key' => getenv('BOT_API_KEY'),
    'bot_username' => $botUsername,
    // Secret key required to access the webhook
    'secret' => getenv('BOT_API_KEY'),

    'commands' => [
        // Define all paths for your custom commands
        'paths' => [
            SYSTEM_COMMANDS_PATH,
            USER_COMMANDS_PATH,
            ADMIN_COMMANDS_PATH,
        ],
        // Here you can set some command specific parameters
        'configs' => [
            'weather' => ['owm_api_key' => getenv('WEATHER_OWM_API_KEY')],

            // e.g. Google geocode/timezone api key for /date command
            'date' => [
                'google_api_key' => getenv('GOOGLE_GEO_API'),
                'collect_api_key' => getenv('COLLECT_API_KEY'),
            ],

        ],
    ],
    // (bool) Only allow webhook access from valid Telegram API IPs.
    'validate_request' => true,
    // (array) When using `validate_request`, also allow these IPs.
    'valid_ips' => [
        '85.238.106.27',
        '159.224.34.168',
    ],

    // Define all IDs of admin users
    'admins' => [
        (int)getenv('ADMIN_TELEGRAM_ID'),
        (int)getenv('ADMIN2_TELEGRAM_ID'),
    ],

    // Enter your MySQL database credentials
    'mysql' => [
        'host' => getenv('DB_HOST'),
        'user' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
        'database' => getenv('DB_DATABASE'),
        'table_prefix' => getenv('DB_TABLE_PREFIX') ?: '',
    ],

    // Set custom Upload and Download paths
    'paths' => [
        'download' => DOWNLOAD_PATH,
        'upload' => UPLOAD_PATH,
    ],

    // Requests Limiter (tries to prevent reaching Telegram API limits)
    'limiter' => ['enabled' => true],
];

if (getenv('BOT_MODE') == 'hook') {
    $botSettings = array_merge($botSettings, [
        'webhook' => [
            // When using webhook, this needs to be uncommented and defined
            'url' => getenv('BOT_HOOK_URL'),
            // Limit maximum number of connections
            'max_connections' => 40,
            // 'allowed_updates' => ['message', 'edited_channel_post', 'callback_query'],
        ],
    ]);
}

$logger = new Logger('TelegramLogger');
$logger->pushHandler(new StreamHandler(LOGS_PATH . DIRECTORY_SEPARATOR . 'global.log', Logger::INFO));
TelegramLog::initialize($logger);