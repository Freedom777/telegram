<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Longman\TelegramBot\TelegramLog;
use Models\AdminCommand;
use Longman\TelegramBot\DB;
use PDO;
use PDOStatement;

class RemindOrderCronCommand extends AdminCommand {
    /**
     * @var string
     */
    protected $name = 'remindordercron';

    /**
     * @var string
     */
    protected $description = 'Create cron for remind user about no Order for last time.';

    /**
     * @var string
     */
    protected $usage = '/remindordercron';

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
        /*$pdoStatement = DB::getPdo()->query('SELECT `user_id`, `text` FROM `message` WHERE `chat_id` = `user_id` AND `entities` LIKE \'%"length":10,"type":"phone_number"%\'', PDO::FETCH_ASSOC);
        $resultAr = [];
        foreach ($pdoStatement as $row) {
            $resultAr [$row['user_id']] = $row ['text'];
        }*/
        if (!empty($amocrmUsersAr)) {
            try {
                $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

                $pipelineId = self::PIPELINE_ID; // id Воронки

                /** @var \DateTime $startSearch */
                $startSearch = new \DateTime(date('Y-m-d 00:00:00'), new \DateTimeZone(self::TIMEZONE));
                $startSearch->modify('-' . getenv('AMOCRM_SUCCESS_ORDER_REMINDER_DAYS') . ' days');

                /** @var \DrillCoder\AmoCRM_Wrap\Lead[] $leads */
                $leads = $amo->searchLeads(null, $pipelineId, [], 0, 0, [], $startSearch);

                $leadUsersAr = [];
                /** @inherited $lead */
                foreach ($leads as $lead) {
                    $leadUsersAr [(int) $lead->getMainContactId()] = (int) $lead->getMainContactId();
                }
                $amocrmUsersAr = array_diff_key($amocrmUsersAr, $leadUsersAr);

                if (!empty($amocrmUsersAr)) {
                    $sth = DB::getPdo()->prepare('
                    INSERT INTO `cron_message` SET 
                    `amocrm_user_id` = :amocrm_user_id,
                    `amocrm_lead_id` = NULL,
                    `amocrm_status_id` = NULL,
                    `chat_id` = :chat_id,
                    `phones` = :phones,
                    `type` = :type,
                    `status` = 0,
                    `created_at` = NOW(),
                    `updated_at` = NOW() 
                ');
                    foreach ($amocrmUsersAr as $amocrmUserId => $chatAr) {
                        $sth->execute([
                            ':amocrm_user_id' => $amocrmUserId,
                            ':chat_id' => $chatAr ['chat_id'],
                            ':phones' =>  $chatAr ['phone'],
                            ':type' => self::REMIND_NO_ORDER,
                        ]);
                    }
                }
            } catch (AmoWrapException $e) {
                TelegramLog::error($e->getMessage());
            }
        }



        /*echo implode('<br />', $leadsAr);

        foreach ($resultAr as $chat_id => $phone) {
            $data = [
                'chat_id' => getenv('CHANNEL_CHAT_ID'),
                'text' => 'Доброй ночи, дорогие клиенты!',
            ];
        }


        $data = [
            'chat_id' => getenv('CHANNEL_CHAT_ID'),
            'text' => $text,
        ];

        Request::sendMessage($data);*/
    }
}