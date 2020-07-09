<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
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
                    $sentMessages [] = $id;
                    break;
                case self::BILL_SENT:
                    $sentMessages [] = $id;
                    break;
                case self::SURVEY_FEEDBACK:
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