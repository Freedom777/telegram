<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommands\CallRequireCommand;
use Longman\TelegramBot\Commands\UserCommands\CatalogCommand;
use Longman\TelegramBot\Commands\UserCommands\HistoryCommand;
use Longman\TelegramBot\Commands\UserCommands\StatusCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlinekeyboardCommand.php
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback_query    = $this->getCallbackQuery();
        $callback_query_id = $callback_query->getId();
        $callback_data     = $callback_query->getData();
        TelegramLog::notice($callback_query_id . ' : ' . var_export($callback_data, true));

        $update = (array) $this->update;
        $update['message'] = $update['callback_query']['message'];
        $update['message']['text'] = $callback_data;
        $update['message']['from']['id'] = $update['callback_query']['from']['id'];

        $result = false;
        switch ($callback_data) {
            /*case 'channel':
                @Likant - инструмент
                $result = $this->getTelegram()->executeCommand('status');
                break;*/
            case '/status':
                // $result = $this->getTelegram()->executeCommand('status');
                $result = (new StatusCommand($this->telegram, new Update($update)))->preExecute();
                break;
            case '/catalog':
                // $result = $this->getTelegram()->executeCommand('catalog');
                $result = (new CatalogCommand($this->telegram, new Update($update)))->preExecute();
                break;
            case '/history':
                $result = (new HistoryCommand($this->telegram, new Update($update)))->preExecute();
                // $result = $this->getTelegram()->executeCommand('history');
                break;

            case '/callrequire':
                $telegram = $this->getTelegram();
                $data = json_decode($telegram->getCustomInput(), true);
                // $data = json_encode(array_merge($data, ['phone' => $callback_data['phone']]));
                // $telegram->setCustomInput($data);

                $answer = [
                    'callback_query_id' => $callback_query_id,
                    'text'              => var_export($data),
                ];

                return Request::answerCallbackQuery($answer);

                $result = (new CallRequireCommand($telegram, new Update($update)))->preExecute();
                break;

        }

        return $result;


        /*$data = [
            'callback_query_id' => $callback_query_id,
            'text'              => 'Hello World!',
            'show_alert'        => $callback_data === 'thumb up',
            'cache_time'        => 5,
        ];

        return Request::answerCallbackQuery($data);*/
    }
}
