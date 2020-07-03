<?php

use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use TelegramBot\TelegramBotManager\Exception\InvalidAccessException;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'settings.php';

try {
    $bot = new TelegramBot\TelegramBotManager\BotManager($botSettings);
    // $bot->setCustomGetUpdatesCallback('handleUpdates');

    $bot->validateSecret();
    if (!$bot->isValidRequest()) {
        throw new InvalidAccessException('Invalid access');
    }
    $bot->setBotExtras();
    /** @var PDOStatement $pdoStatement */
    /*$pdoStatement = DB::getPdo()->query('SELECT `user_id`, `text` FROM `message` WHERE `chat_id` = `user_id` AND `entities` LIKE \'%"length":10,"type":"phone_number"%\'', PDO::FETCH_ASSOC);
    $resultAr = [];
    foreach ($pdoStatement as $row) {
        $resultAr [$row['user_id']] = $row ['text'];
    }*/

    /*foreach ($resultAr as $chat_id => $phone) {
        $data = [
            'chat_id' => getenv('CHANNEL_CHAT_ID'),
            'text' => 'Доброй ночи, дорогие клиенты!',
        ];
    }*/

    $data = [
        'chat_id' => getenv('CHANNEL_CHAT_ID'),
        'text' => 'Доброй ночи, дорогие клиенты!',
    ];

    Request::sendMessage($data);

    // TelegramLog::notice(var_export($result, true));

} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Silence is golden!
    // Uncomment this to catch log initialisation errors
    echo $e;

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Log telegram errors
    TelegramLog::error($e);
} catch (InvalidAccessException $e) {
    // Log telegram errors
    TelegramLog::error($e);
}