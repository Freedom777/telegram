<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Commands\UserCommands\SurveySuccessCommand;
use Models\AdminCommand;
use Longman\TelegramBot\Request;
use Models\BasePdo;
use Models\Logic;

class SendCronCommand extends AdminCommand {
    /**
     * @var string
     */
    protected $name = 'sendcron';

    /**
     * @var string
     */
    protected $description = 'Send Cron messages.';

    /**
     * @var string
     */
    protected $usage = '/sendcron';

    public function execute()
    {
        /** @var array $users */
        $users = Logic::getAmocrmUsers([
            'fields' => ['amocrm_user_id', 'chat_id', 'phone'],
            'group' => 'amocrm_user_id'
        ]);

        $amocrmUsersAr = [];
        foreach ($users as $row) {
            $amocrmUsersAr [(int) $row['amocrm_user_id']] = [
                'chat_id' => (int) $row ['chat_id'],
                'phone' => $row ['phone']
            ];
        }

        $cronMessages = Logic::getCronMessages([
            'filters' => ['status' => [self::$STATUS_TO_SEND, self::$STATUS_REMIND]]
        ]);
        $messages = [];
        foreach ($cronMessages as $row) {
            if (in_array((int) $row ['amocrm_user_id'], $amocrmUsersAr)) {
                $messages [(int) $row['id']] = [
                    'amocrm_user_id' => (int) $row ['amocrm_user_id'],
                    'amocrm_lead_id' => (int) $row ['amocrm_lead_id'],
                    'amocrm_status_id' => (int) $row ['amocrm_status_id'],
                    'chat_id' => $amocrmUsersAr [(int) $row ['amocrm_user_id']] ['chat_id'],
                    'type' => $row ['type'],
                    'phone' => $amocrmUsersAr [(int) $row ['amocrm_user_id']] ['phone'],
                    'status' => $row ['status'],
                ];
            }
        }

        $sentMessages = [];
        foreach ($messages as $id => $message) {
            $data = [
                'chat_id' => $message ['chat_id'],
            ];

            switch ($message ['type']) {
                case self::REMIND_NO_ORDER:
                    $result = $this->getTelegram()->executeCommand('remindorder');

                    if (!empty($result)) {
                        $sentMessages [] = $id;
                    }
                    break;
                case self::BILL_SENT:
                    $text = '';
                    if ($message ['status'] == self::$STATUS_TO_SEND) {
                        $text = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'billsent.php';
                    } elseif ($message ['status'] == self::$STATUS_REMIND) {
                        $text = vsprintf(require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'billsent_again.php', [$message ['amocrm_lead_id']]);
                    }
                    $data ['text'] = $text;

                    Request::sendMessage($data);
                    $sentMessages [] = $id;
                    break;
                case self::SURVEY_FEEDBACK:
                    $result = $this->getTelegram()->executeCommand('surveysuccess');

                    if (!empty($result)) {
                        $sentMessages [] = $id;
                    }
                    break;
                case self::SURVEY_NOT_BOUGHT:
                    /*//Preparing Response
                    $msg = $this->getMessage();
                    $text    = trim($msg->getText(true));
                    $question = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'surveyfail.php';

                    //Conversation start
                    $this->conversation = new Conversation($message ['chat_id'], $message ['chat_id'],
                        (new SurveyFailCommand($this->getTelegram()))->getName()
                    );

                    // $result = Request::emptyResponse();
                    if ($text === '') {
                        $this->conversation->update();

                        $data ['text'] = $question;
                        Request::sendMessage($data);
                        break;
                    }

                    $this->conversation->stop();

                    $data = [
                        'text' => 'Спасибо за обратную связь!',
                    ];
                    Request::sendMessage($data);*/
                    $result = $this->getTelegram()->executeCommand('surveyfail');

                    if (!empty($result)) {
                        $sentMessages [] = $id;
                    }
                    break;
            }
        }

        if (!empty($sentMessages)) {
            Logic::updateCronMessage([
                'status' => self::$STATUS_SENT,
                'updated_at' => BasePdo::now(),
            ], [
                'id' => $sentMessages,
                'status' => self::$STATUS_TO_SEND,
            ]);

            Logic::updateCronMessage([
                'status' => self::$STATUS_REMINDED,
                'updated_at' => BasePdo::now(),
            ], [
                'id' => $sentMessages,
                'status' => self::$STATUS_REMIND,
            ]);
        }

    }
}