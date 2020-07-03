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
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;

/**
 * User "/status" command
 *
 * Get status info for any place.
 * This command requires an API key to be set via command config.
 */
class StatusCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'status';

    /**
     * @var string
     */
    protected $description = 'Show status by phone';

    /**
     * @var string
     */
    protected $usage = '/status';

    /**
     * @var string
     */
    protected $version = '1.0.0';

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
        $answerText = '';

        $text    = trim($message->getText(true));

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        //cache data from the tracking session if any
        $state = 0;
        if (isset($notes['state'])) {
            $state = $notes['state'];
        }

        $result = Request::emptyResponse();

        switch ($state) {
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text'] = 'Введите номер телефона (10 цифр):';
                    $data['reply_markup'] = Keyboard::remove(['selective' => true]);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['phone'] = $text;
                $text = '';

            case 1:
                $phone = $notes['phone'];
                if ($phone !== '' && is_numeric($phone) && 10 == strlen($phone)) {
                    $notes['state'] = 1;
                    $this->conversation->update();
                    try {
                        $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));
                    } catch (AmoWrapException $e) {
                        $answerText = 'Ошибка при подключении к хранилищу.';
                    }

                    if (empty($answerText)) {
                        /*// Получаем список воронок
                        $pipelines = AmoCRM::getPipelinesName();

                        // Берем id первой воронки
                        $pipelineId = key($pipelines);

                        // Получаем список статусов
                        $statuses = AmoCRM::getStatusesName($pipelineId);*/

                        $contacts =  $amo->searchContacts($phone); //Ищем контакт по телефону и почте
                        if (!empty($contacts)) {
                            $contact = current($contacts);
                            $leads = $contact->getLeads();
                            if (!empty($leads)) {
                                foreach ($leads as $lead) {
                                    $answerText .= $lead->getName() . ' : ' . $lead->getStatusName() . PHP_EOL;
                                }
                            }
                        } else {
                            $answerText = 'Контакт не найден.';
                        }
                    }
                    $this->conversation->stop();
                } else {
                    $answerText = 'Вы должны указать 10 цифр в качестве номера телефона.';
                    $notes['state'] = 0;
                }
                $data['text'] = $answerText;
                $result = Request::sendMessage($data);
        }

        return $result;

        /*$phone = trim($message->getText(true));
        if ($phone !== '') {
            try {
                $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));
            } catch (AmoWrapException $e) {
                $answerText = 'Ошибка при подключении к AmoCRM.';
            }

            if (empty($answerText)) {
                $contacts =  $amo->searchContacts($phone); //Ищем контакт по телефону и почте
                if (!empty($contacts)) {
                    $contact = current($contacts);
                    $leads = $contact->getLeads();
                    if (!empty($leads)) {
                        foreach ($leads as $lead) {
                            $answerText .= $lead->getName() . ' : ' . $lead->getStatusName() . PHP_EOL;
                        }
                    }
                } else {
                    $answerText = 'Контакт не найден.';
                }
            }
        } else {
            $answerText = 'Вы должны указать телефон в формате: /status <phone>';
        }*/

        /*$data = [
            'chat_id' => $chat_id,
            'text'    => $answerText,
        ];

        return Request::sendMessage($data);*/
    }
}