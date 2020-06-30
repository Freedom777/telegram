<?php
/**
 * README
 * This configuration file is intended to be used as the main script for the PHP Telegram Bot Manager.
 * Uncommented parameters must be filled
 *
 * For the full list of options, go to:
 * https://github.com/php-telegram-bot/telegram-bot-manager#set-extra-bot-parameters
 */

use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ChosenInlineResult;
use Longman\TelegramBot\Entities\InlineQuery;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
// require_once __DIR__ . '/df.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$botUsername = getenv('BOT_USERNAME');

$botSettings = [
    // Add you bot's API key and name
    'api_key'      => getenv('BOT_API_KEY'),
    'bot_username' => $botUsername,
    // Secret key required to access the webhook
    'secret'       => getenv('BOT_API_KEY'),

    'commands' => [
        // Define all paths for your custom commands
        'paths'   => [
            __DIR__ . '/Commands',
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
    'admins'       => [
        (int) getenv('ADMIN_TELEGRAM_ID'),
    ],

    // Enter your MySQL database credentials
    'mysql'        => [
        'host'          => getenv('DB_HOST'),
        'user'          => getenv('DB_USERNAME'),
        'password'      => getenv('DB_PASSWORD'),
        'database'      => getenv('DB_DATABASE'),
        'table_prefix'  => getenv('DB_TABLE_PREFIX') ?: '',
    ],

    // Set custom Upload and Download paths
    'paths'    => [
        'download' => __DIR__ . '/Download',
        'upload'   => __DIR__ . '/Upload',
    ],

    // Requests Limiter (tries to prevent reaching Telegram API limits)
    'limiter'      => ['enabled' => true],

    // 'custom_input'   => 'setCustomInput',
];

if (getenv('BOT_MODE') == 'hook') {
    $botSettings = array_merge($botSettings, [
        'webhook'      => [
            // When using webhook, this needs to be uncommented and defined
            'url' => getenv('BOT_HOOK_URL'),
            // Use self-signed certificate
            // 'certificate' => __DIR__ . '/server.crt',
            // Limit maximum number of connections
            'max_connections' => 40,
            // 'allowed_updates' => ['message', 'edited_channel_post', 'callback_query'],
        ],
    ]);
}


// Add you bot's username (also to be used for log file names)
try {
    $logger = new Logger('TelegramLogger');
    $logger->pushHandler(new StreamHandler(__DIR__ . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . 'global.log', Logger::INFO));
    TelegramLog::initialize($logger);

    $bot = new TelegramBot\TelegramBotManager\BotManager($botSettings);
    $bot->setCustomGetUpdatesCallback('handleUpdates');
    // $bot->setBotExtras()
        /*function ($get_updates_response):string {
            TelegramLog::notice('HANDLE');
            return '';
        }
    );*/
        // 'handleUpdates');
    // Run the bot!
    $bot->run();
    // Text message
    //$lastUpdate = DB::selectTelegramUpdate(1, $bot->getTelegram()->getLastUpdateId());
    // TelegramLog::notice(var_export($lastUpdate, true));

    /*if (null === $bot->getTelegram()->getCommandObject()) {

    }*/
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    echo $e;
    // Log telegram errors
    TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Silence is golden!
    // Uncomment this to catch log initialisation errors
    echo $e;
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

            if (in_array($update_content->getType(), ['text', 'phone_number'])) {
                $answerText = '';
                $inputText = $update_content->getText();

                /*Request::sendMessage([
                    'chat_id' => $chat_id,
                    'text'    => $answerText,
                ]);*/
            }

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