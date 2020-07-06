<?php
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use TelegramBot\TelegramBotManager\Exception\InvalidAccessException;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'settings.php';

// Your command(s) to run, pass it just like in a message (arguments supported)
$commands = [
    '/surveysuccess'
    /*'/whoami',
    "/echo I'm a bot!",*/
];


try {
    $bot = new TelegramBot\TelegramBotManager\BotManager($botSettings);

    $bot->validateSecret();
    if (!$bot->isValidRequest()) {
        throw new InvalidAccessException('Invalid access');
    }
    $bot->setBotExtras();

    // Enable admin users
    // $telegram->enableAdmins($admin_users);
    // Requests Limiter (tries to prevent reaching Telegram API limits)
    $bot->getTelegram()->enableLimiter();

    // Run user selected commands
    $bot->getTelegram()->runCommands($commands);

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    //echo $e;
    // Log telegram errors
    TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Silence is golden!
    // Uncomment this to catch log initialisation errors
    //echo $e;
}