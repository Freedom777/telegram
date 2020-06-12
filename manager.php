<?php
/**
 * README
 * This configuration file is intended to be used as the main script for the PHP Telegram Bot Manager.
 * Uncommented parameters must be filled
 *
 * For the full list of options, go to:
 * https://github.com/php-telegram-bot/telegram-bot-manager#set-extra-bot-parameters
 */

// Load composer
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Longman\TelegramBot\Commands\SystemCommands\CurrencyCommand;
use Longman\TelegramBot\Commands\UserCommands\WeatherCommand;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ChosenInlineResult;
use Longman\TelegramBot\Entities\InlineQuery;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Google\Cloud\Storage\StorageClient;

require_once __DIR__ . '/vendor/autoload.php';
// require_once __DIR__ . '/df.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$botUsername = getenv('BOT_USERNAME');

$botSettings = [
    // Add you bot's API key and name
    'api_key'      => getenv('BOT_API_KEY'),
    'bot_username' => $botUsername,

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
        '85.238.106.27'
    ],

    // Define all IDs of admin users
    'admins'       => [
        getenv('ADMIN_TELEGRAM_ID'),
    ],

    // Enter your MySQL database credentials
    'mysql'        => [
        'host'          => getenv('DB_HOST'),
        'user'          => getenv('DB_USERNAME'),
        'password'      => getenv('DB_PASSWORD'),
        'database'      => getenv('DB_DATABASE'),
        'table_prefix'  => getenv('DB_TABLE_PREFIX') ?: '',
    ],

    // Logging (Error, Debug and Raw Updates)
    'logging'  => [
        'debug'  => __DIR__ . '/Logs/' . $botUsername . '_debug.log',
        'error'  => __DIR__ . '/Logs/' . $botUsername . '_error.log',
        'update' => __DIR__ . '/Logs/' . $botUsername . '_update.log',
    ],

    // Set custom Upload and Download paths
    'paths'    => [
        'download' => __DIR__ . '/Download',
        'upload'   => __DIR__ . '/Upload',
    ],

    // Requests Limiter (tries to prevent reaching Telegram API limits)
    'limiter'      => ['enabled' => true],
];

if (getenv('BOT_MODE') == 'hook') {
    $botSettings = array_merge($botSettings, [

        // Secret key required to access the webhook
        'secret'       => getenv('BOT_API_KEY'),

        'webhook'      => [
            // When using webhook, this needs to be uncommented and defined
            'url' => getenv('BOT_HOOK_URL'),
            // Use self-signed certificate
            // 'certificate' => __DIR__ . '/server.crt',
            // Limit maximum number of connections
            'max_connections' => 40,
        ],

    ]);
}


// Add you bot's username (also to be used for log file names)
try {
    // Authenticating with a keyfile path.
    $storage = new StorageClient([
        'keyFilePath' => __DIR__ . '/small-talk-iohimh-43608e0508e0.json'
    ]);

    $bot = new TelegramBot\TelegramBotManager\BotManager($botSettings);

    $bot->setCustomGetUpdatesCallback('handleUpdates');

    // Run the bot!
    $bot->run();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    echo $e;
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Silence is golden!
    // Uncomment this to catch log initialisation errors
    echo $e;
}

function handleUpdates($get_updates_response){
    if (!$get_updates_response->isOk()) {
        return sprintf(
            '%s - Failed to fetch updates' . PHP_EOL . '%s',
            date('Y-m-d H:i:s'),
            $get_updates_response->printError(true)
        );
    }

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
            $text    .= ";{$update_content->getType()}";
            if ('text' == $update_content->getType()) {
                $inputText = $update_content->getText();
                /*echo $inputText;
                if ('курс' == mb_strtolower(mb_substr($inputText, 0, 4))) {
                    (new CurrencyCommand)->execute();
                } elseif('погода' == mb_strtolower(mb_substr($inputText, 0, 6))) {
                    (new WeatherCommand)->getWeatherByLocation(substr($inputText, 8));
                } else {*/
                    $answers = detect_intent_texts('small-talk-iohimh', $inputText, $chat_id);

                    $result = Request::sendMessage([
                        'chat_id' => $chat_id,
                        'text'    => $answers[0],
                    ]);
                // }
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

/**
 * Returns the result of detect intent with texts as inputs.
 * Using the same `session_id` between requests allows continuation
 * of the conversation.
 * @param $projectId
 * @param $texts
 * @param $sessionId
 * @param string $languageCode
 *
 * @throws \Google\ApiCore\ApiException
 */
function detect_intent_texts($projectId, $texts, $sessionId, $languageCode = 'ru')
{
    // new session
    $sessionsClient = new SessionsClient();
    $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());
    printf('Session path: %s' . PHP_EOL, $session);
    if (!is_array($texts)) {
        $texts = [$texts];
    }

    // query for each string in array
    $answers = [];
    foreach ($texts as $text) {
        // create text input
        $textInput = new TextInput();
        $textInput->setText($text);
        $textInput->setLanguageCode($languageCode);

        // create query input
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        // get response and relevant info
        $response = $sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();
        $queryText = $queryResult->getQueryText();

        $intent = $queryResult->getIntent();
        $displayName = 'Unknown';
        if (null !== $intent && !empty($intent->getDisplayName())) {
            $displayName = $intent->getDisplayName();
        }
        $confidence = $queryResult->getIntentDetectionConfidence();
        $fulfilmentText = $queryResult->getFulfillmentText();

        // output relevant info
        print(str_repeat("=", 20) . PHP_EOL);
        printf('Query text: %s' . PHP_EOL, $queryText);
        printf('Detected intent: %s (confidence: %f)' . PHP_EOL, $displayName,
            $confidence);
        print(PHP_EOL);
        printf('Fulfilment text: %s' . PHP_EOL, $fulfilmentText);

        $answers [] = $fulfilmentText;
    }

    $sessionsClient->close();
    return $answers;
}