<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Models\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\DB;
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
class StartCommand extends UserCommand
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
     * @var int
     */
    protected $chat_id;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $text;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $this->prepareInput();

        //Preparing Response
        $data = [
            'chat_id' => $this->chat_id,
        ];
        $answerText = '';
        // TelegramLog::notice($text);

        $result = Request::emptyResponse();
        $state = $this->getState();

        switch ($state) {
            // Приветствие, запрос номера телефона
            case 0:
                if ($this->text === '') {
                    $this->notes ['state'] = 0;
                    $this->conversation->update();

                    $data ['text'] = 'Здравствуйте, я чат-бот Ликант - инструмент.'; // Я умею отдавать статус заказа по номеру телефона. Нажмите "Статус заказа" для продолжения.';
                    $data ['text'] .= PHP_EOL . 'Введите номер телефона (10 цифр, пример 050*******) для авторизации:';
                    $data ['reply_markup'] = Keyboard::remove(['selective' => true]);

                    $result = Request::sendMessage($data);
                    break;
                }

                // $notes['choice'] = $this->text;
                $this->notes ['phone'] = $this->text;
                $this->text = '';

            case 1:
                // Проверка телефона на существование в базе, получение списка сделок
                $phone = $this->notes ['phone'];
                if ($phone !== '' && is_numeric($phone) && $phone > 0 && 10 == strlen($phone)) {
                    $this->notes ['state'] = 1;
                    $this->conversation->update();

                    try {
                        $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));
                    } catch (AmoWrapException $e) {
                        $answerText = self::ERROR_AMOCRM;
                    }

                    if (empty($answerText)) {
                        $contacts =  $amo->searchContacts($phone); //Ищем контакт по телефону и почте
                        if (!empty($contacts)) {
                            $contact = current($contacts);
                            $this->checkInsertUser($phone, $contact->getId());

                            $leads = $contact->getLeads();
                            $this->notes ['leads'] = [];
                            if (!empty($leads)) {
                                foreach ($leads as $lead) {
                                    $this->notes ['leads'] [$lead->getName()] = $lead->getStatusName();
                                    // $answerText .= $lead->getName() . ' : ' . $lead->getStatusName() . PHP_EOL;
                                }
                            }

                            $_SESSION ['user'] = [
                                'phone' => $phone,
                                'userId' => $contact->getId(),
                                'leads' => $this->notes ['leads'],
                            ];

                            $answerText = self::SUCCESS_LOGIN;

                        } else {
                            $answerText = self::ERROR_PHONE_NOT_FOUND;
                            $this->conversation->stop();
                        }
                    }
                } else {
                    $answerText = 'Вы должны указать 10 цифр в качестве номера телефона.';
                    $this->notes ['state'] = 1;
                }
                $data ['text'] = $answerText;
                $result = Request::sendMessage($data);
            case 2:
                // При успешной авторизации вывод меню
                if ($this->text === '') {
                    $this->notes ['state'] = 2;
                    $this->conversation->update();
                    $data ['reply_markup'] = (new InlineKeyboard([
                        new InlineKeyboardButton(['callback_data' => '/status', 'text' => self::MENU_ORDER_STATUS]),
                        new InlineKeyboardButton(['callback_data' => '/history', 'text' => self::MENU_HISTORY]),
                        new InlineKeyboardButton(['callback_data' => '/catalog', 'text' => self::MENU_CATALOG]),
                    ]))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                }

                $this->notes ['choice'] = $this->text;
                $this->text = '';

                $result = Request::sendMessage($data);

            case 3:
                $choice = $this->notes ['choice'];
                if ($choice !== '') {
                    $this->notes ['state'] = 3;
                    $this->conversation->update();
                    switch ($choice) {
                        case self::MENU_ORDER_STATUS:
                            $this->notes ['choice'] = '/status';
                            $this->conversation->stop();
                            $result = $this->getTelegram()->executeCommand('status');
                        // $result = (new StatusCommand($this->getTelegram()))->execute();
                        break;
                    }
                    // $data['text'] = $choice;
                    // $result = Request::sendMessage($data);

                }
        }

        return $result;
    }


}
/*// Получаем список воронок
                            $pipelines = AmoCRM::getPipelinesName();

                            // Берем id первой воронки
                            $pipelineId = key($pipelines);

                            // Получаем список статусов
                            $statuses = AmoCRM::getStatusesName($pipelineId);*/