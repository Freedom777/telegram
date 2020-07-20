<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Commands\AdminCommand;
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

    /**
     * @var string
     */
    protected $version = '1.0.0';

    const REMIND_NO_ORDER = 'remind_no_order';
    const BILL_SENT = 'bill_sent';
    const SURVEY_FEEDBACK = 'survey_feedback';
    const SURVEY_NOT_BOUGHT = 'survey_not_bought';

    const STATUSES = [
        29361424    => 'Неразобранное',
        29361427    => 'Новое обращение',
        29361430    => 'Заказ Согласован',
        29361433    => 'Договор/счет отправлен',
        29399374    => 'Передан на склад',
        29548384    => 'Заказ Списан',
        31654648    => 'заказ собран с дефицитом',
        30617692    => 'заказ собран без дефицита',
        29362315    => 'Товар отгружен',
        29362318    => 'НЕзавершенные',
        142         => 'Успешно реализовано',
        143         => 'Закрыто и не реализовано',
    ];

    const TIMEZONE = 'Europe/Kiev';

    const STATUS_TO_SEND = 0;
    const STATUS_SENT = 1;

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
            WHERE `status` = ' . self::STATUS_TO_SEND
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
                ];
            }
        }

        $sentMessages = [];
        foreach ($messages as $id => $message) {
            switch ($message ['type']) {
                case self::REMIND_NO_ORDER:
                    $text = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'remind.php';
                    $data = [
                        'chat_id' => $message ['chat_id'],
                        'text'    => $text,
                    ];
                    Request::sendMessage($data);

                    $sentMessages [] = $id;
                    break;
                case self::BILL_SENT:
                    $text = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'billsent.php';
                    $data = [
                        'chat_id' => $message ['chat_id'],
                        'text'    => $text,
                    ];
                    Request::sendMessage($data);

                    $sentMessages [] = $id;
                    break;
                case self::SURVEY_FEEDBACK:
                    //Preparing Response
                    $msg = $this->getMessage();
                    $text    = trim($msg->getText(true));
                    $data = [
                        'chat_id' => $message ['chat_id'],
                    ];
                    $question = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'surveysuccess.php';
                    $answers = ['1', '2', '3', '4', '5'];

                    //Conversation start
                    $this->conversation = new Conversation($message ['chat_id'], $message ['chat_id'], $this->getName());

                    // $result = Request::emptyResponse();
                    if ($text === '' || !in_array($text, $answers, true)) {
                        $this->conversation->update();

                        $data['reply_markup'] = (new Keyboard($answers))
                            ->setResizeKeyboard(true)
                            ->setOneTimeKeyboard(true)
                            ->setSelective(true);

                        $data ['text'] = $question;
                        if ($text !== '') {
                            $data ['text'] = $question;
                        }

                        Request::sendMessage($data);
                    }

                    $this->conversation->stop();

                    $data = [
                        'reply_markup' => Keyboard::remove(['selective' => true]),
                        'chat_id' => $message ['chat_id'],
                        'text' => 'Спасибо за обратную связь, Вы выбрали ' . $text,
                    ];
                    Request::sendMessage($data);


                    $sentMessages [] = $id;
                    break;
                case self::SURVEY_NOT_BOUGHT:
                    $sentMessages [] = $id;
                    break;
            }
        }

        if (!empty($sentMessages)) {
            $sth = DB::getPdo()->prepare('
                UPDATE `cron_message` SET 
                    `status` = ' . self::STATUS_SENT . ',
                    `updated_at` = NOW()
                WHERE `id` IN (' . implode(',', $sentMessages). ')
            ');
            $sth->execute($sth);
        }

    }
}