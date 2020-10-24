<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Commands\UserCommands\SurveySuccessCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Models\AdminCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Request;
use PDO;
use PDOStatement;

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
        /** @var PDOStatement $pdoStatement */
        $pdoStatement = DB::getPdo()->query('
            SELECT `amocrm_user_id`, `chat_id`, `phone`
            FROM `amocrm_user`
            GROUP BY `amocrm_user_id`
            ORDER BY `id` DESC'
            , PDO::FETCH_ASSOC);
        $amocrmUsersAr = [];
        foreach ($pdoStatement as $row) {
            $amocrmUsersAr [(int) $row['amocrm_user_id']] = [
                'chat_id' => (int) $row ['chat_id'],
                'phone' => $row ['phone']
            ];
        }

        $pdoStatement = DB::getPdo()->query('
            SELECT *
            FROM `cron_message`
            WHERE `status` IN (' . self::STATUS_TO_SEND . ',' . self::STATUS_REMIND . ')'
            , PDO::FETCH_ASSOC);
        $messages = [];
        foreach ($pdoStatement as $row) {
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
                    if ($message ['status'] == self::STATUS_TO_SEND) {
                        $data ['text'] = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'remind.php';
                    } elseif ($message ['status'] == self::STATUS_REMIND) {
                        $data ['text'] = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'remind_again.php';
                    }
                    $data ['text'] .= PHP_EOL . getenv('AMOCRM_MANAGER_PHONE_1') . PHP_EOL . getenv('AMOCRM_MANAGER_PHONE_2');

                    Request::sendMessage($data);
                    $sentMessages [] = $id;
                    break;
                case self::BILL_SENT:
                    $text = '';
                    if ($message ['status'] == self::STATUS_TO_SEND) {
                        $text = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'billsent.php';
                    } elseif ($message ['status'] == self::STATUS_REMIND) {
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
            $sth = DB::getPdo()->prepare('
                UPDATE `cron_message` SET 
                    `status` = ' . self::STATUS_SENT . ',
                    `updated_at` = NOW()
                WHERE `status` = ' . self::STATUS_TO_SEND . ' AND `id` IN (' . implode(',', $sentMessages). ')
            ');
            $sth->execute($sth);

            $sth = DB::getPdo()->prepare('
                UPDATE `cron_message` SET 
                    `status` = ' . self::STATUS_REMINDED . ',
                    `updated_at` = NOW()
                WHERE `status` = ' . self::STATUS_REMIND . ' AND `id` IN (' . implode(',', $sentMessages). ')
            ');
            $sth->execute($sth);
        }

    }
}