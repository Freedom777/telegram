<?php

session_start();

use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ChosenInlineResult;
use Longman\TelegramBot\Entities\InlineQuery;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use Models\BasePdo;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'settings.php';

// Add you bot's username (also to be used for log file names)
try {
    $dbh = new PDO('mysql:host=' . $botSettings['mysql']['host'] . ';dbname=' . $botSettings['mysql']['database'],
        $botSettings['mysql']['user'], $botSettings['mysql']['password']);
    $admins = array_column(BasePdo::select('amocrm_user', [
        'fields' => 'chat_id',
        'where' => ['amocrm_user_type' => 'admin']
    ]), 'chat_id');

    Request::sendMessage([
        'chat_id' => (int)getenv('ADMIN_TELEGRAM_ID'),
        'text'    => 'aaa' . var_export($admins, true),
    ]);
    $dbh = null;
    array_walk($admins, function (&$item) {
        $item = (int) $item;
    });
    $botSettings ['admins'] = $admins;

    $bot = new TelegramBot\TelegramBotManager\BotManager($botSettings);
    $bot->setCustomGetUpdatesCallback('handleUpdates');

    // Run the bot!
    $bot->run();
    // Text message
    //$lastUpdate = DB::selectTelegramUpdate(1, $bot->getTelegram()->getLastUpdateId());
    // TelegramLog::notice(var_export($lastUpdate, true));

    /*if (null === $bot->getTelegram()->getCommandObject()) {

    }*/
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Log telegram errors
    TelegramLog::error($e);
} catch (\Exception $e) {
    // Log telegram errors
    TelegramLog::error($e);
}

/*function handleUpdates($get_updates_response):string {
    TelegramLog::notice('HANDLE');
    return '';
}*/

/**
 * @param \Longman\TelegramBot\Entities\ServerResponse $get_updates_response
 * @return string
 * @throws \Longman\TelegramBot\Exception\TelegramException
 */
function handleUpdates($get_updates_response):string {
    TelegramLog::notice('HANDLE');
    if (!$get_updates_response->isOk()) {
        $error = sprintf(
            '%s - Failed to fetch updates' . PHP_EOL . '%s',
            date('Y-m-d H:i:s'),
            $get_updates_response->printError(true)
        );
        TelegramLog::error($error);

        return $error;
    }
    TelegramLog::notice('RESULT PARSE');
    /** @var Update[] $results */
    $results = array_filter((array) $get_updates_response->getResult());

    $output = sprintf(
        '%s - Updates processed: %d' . PHP_EOL,
        date('Y-m-d H:i:s'),
        count($results)
    );

    foreach ($results as $result) {
        $update_content = $result->getUpdateContent();

        $chat_id = 'n/a';
        $text    = $result->getUpdateType();

        if ($update_content instanceof Message) {
            /** @var Message $update_content */
            $chat_id = $update_content->getChat()->getId();
            $text    .= ';' . $update_content->getType();

            Request::sendMessage([
                'chat_id' => $chat_id,
                'text'    => 'Анализирую тип ' . $update_content->getType() . ' ...' . PHP_EOL,
            ]);

            //if (in_array($update_content->getType(), ['text', 'phone_number'])) {
            //    $answerText = '';
            //    $inputText = $update_content->getText();

                /*Request::sendMessage([
                    'chat_id' => $chat_id,
                    'text'    => $answerText,
                ]);*/
            //}

        } elseif ($update_content instanceof InlineQuery || $update_content instanceof ChosenInlineResult) {
            /** @var InlineQuery|ChosenInlineResult $update_content */
            $chat_id = $update_content->getFrom()->getId();
            $text    .= ";{$update_content->getQuery()}";
        } elseif ($update_content instanceof CallbackQuery) {
            /** @var CallbackQuery $update_content */
            $chat_id = $update_content->getMessage()->getChat()->getId();
            $text    .= ";{$update_content->getData()}";
        }

        $output .= sprintf(
            '%d: <%s>' . PHP_EOL,
            $chat_id,
            preg_replace('/\s+/', ' ', trim($text))
        );
    }

    return $output;
}