<?php
/**
 * README
 * This file is intended to unset the webhook.
 * Uncommented parameters must be filled
 */

// Load composer
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Add you bot's API key and name
$botApiKey   = getenv('BOT_API_KEY');
$botUsername = getenv('BOT_USERNAME');

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($botApiKey, $botUsername);

    // Delete webhook
    $result = $telegram->deleteWebhook();

    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
}
