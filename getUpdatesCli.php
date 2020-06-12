<?php
require __DIR__ . '/vendor/autoload.php';

$bot_api_key  = '1164525105:AAHzwqQQs-dWAyxjNggbdJEh4JJBk2sG8Cg';
$bot_username = 'FreedomBuddyBot';

$mysql_credentials = [
    'host'     => 'localhost',
    'port'     => 3306, // optional
    'user'     => 'root',
    'password' => '',
    'database' => 'telegram',
];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Enable MySQL
    $telegram->enableMySql($mysql_credentials);

    // Handle telegram getUpdates request
    $telegram->handleGetUpdates();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    file_put_contents(__DIR__ . '/logs/telegram.log', date('Y-m-d H:i:s') . ' ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    // log telegram errors
    // echo $e->getMessage();
}