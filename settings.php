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

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
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
            __DIR__ . '/Commands',
            __DIR__ . '/UserCommands',
            __DIR__ . '/AdminCommands',
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
        'download' => __DIR__ . '/Download',
        'upload' => __DIR__ . '/Upload',
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
$logger->pushHandler(new StreamHandler(__DIR__ . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . 'global.log', Logger::INFO));
TelegramLog::initialize($logger);