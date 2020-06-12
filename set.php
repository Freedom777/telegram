<?php
/**
 * README
 * This file is intended to set the webhook.
 * Uncommented parameters must be filled
 */

// Load composer
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Add you bot's API key and name
$botApiKey   = getenv('BOT_API_KEY');
$botUsername = getenv('BOT_USERNAME');

// Define the URL to your hook.php file
$hookUrl     = getenv('BOT_HOOK_URL');

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($botApiKey, $botUsername);

    // Set webhook
    $result = $telegram->setWebhook($hookUrl);

    // To use a self-signed certificate, use this line instead
    //$result = $telegram->setWebhook($hook_url, ['certificate' => $certificate_path]);

    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
}
