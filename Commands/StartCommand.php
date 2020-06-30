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
use Longman\TelegramBot\Commands\UserCommands\StatusCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat    = $message->getChat();
        $user    = $message->getFrom();

        $chat_id = $chat->getId();
        $user_id = $user->getId();

        //Preparing Response
        $data = [
            'chat_id' => $chat_id,
        ];
        $text    = trim($message->getText(true));
        TelegramLog::notice($text);

        $result = Request::emptyResponse();
        $data ['text'] = 'Здравствуйте, я чат-бот Ликант - инструмент. Я умею отдавать статус заказа по номеру телефона. Нажмите "Статус заказа" для продолжения.';


        if (true == false) {
            //Conversation start
            $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

            $notes = &$this->conversation->notes;
            !is_array($notes) && $notes = [];

            //cache data from the tracking session if any
            $state = 0;
            if (isset($notes['state'])) {
                $state = $notes['state'];
            }

            switch ($state) {
                case 0:
                    if ($text === '') {
                        $notes['state'] = 0;
                        $this->conversation->update();

                        $data ['text'] = 'Здравствуйте, я чат-бот Ликант - инструмент. Я умею отдавать статус заказа по номеру телефона. Нажмите /status для продолжения.';
                        $data ['reply_markup'] = (new Keyboard([
                            new KeyboardButton([/*'callback_data' => '/status', */ 'text' => 'Статус заказа'])
                        ]))
                            ->setResizeKeyboard(true)
                            ->setOneTimeKeyboard(true)
                            ->setSelective(true);
                        $result = Request::sendMessage($data);
                    }

                    $notes['choice'] = $text;
                    $text = '';

                case 1:
                    $choice = $notes['choice'];
                    if ($choice !== '') {
                        // $notes['state'] = 0;
                        $this->conversation->update();
                        switch ($choice) {
                            case 'Статус заказа':
                                $notes['choice'] = '/status';
                                // $this->conversation->stop();
                                // $notes['state'] = 0;
                                $result = $this->getTelegram()->executeCommand('status');
                            // $result = (new StatusCommand($this->getTelegram()))->execute();
                        }
                        // $data['text'] = $choice;
                        // $result = Request::sendMessage($data);

                    }
                    break;
            }
        }

        // (new StatusCommand($this->getTelegram()))->execute()
        $data ['reply_markup'] = (new InlineKeyboard([
            new InlineKeyboardButton(['callback_data' => '/status', 'text' => 'Статус заказа']),
            new InlineKeyboardButton(['callback_data' => '/catalog', 'text' => 'Каталог']),
            // new InlineKeyboardButton(['callback_data' => '/start status', 'text' => 'Статус заказа test']),

        ]))
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);
        $result = Request::sendMessage($data);


        // Working code
        /*$data ['reply_markup'] = (new Keyboard([
            new KeyboardButton(['text' => '/status'])
        ]))
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);
        $result = Request::sendMessage($data);*/

        return $result;
    }
}
