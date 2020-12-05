<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Longman\TelegramBot\TelegramLog;
use Models\AdminCommand;
use Longman\TelegramBot\DB;
use Models\Logic;
use Models\Queries;
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

        /*$pdoStatement = DB::getPdo()->query('SELECT `user_id`, `text` FROM `message` WHERE `chat_id` = `user_id` AND `entities` LIKE \'%"length":10,"type":"phone_number"%\'', PDO::FETCH_ASSOC);
        $resultAr = [];
        foreach ($pdoStatement as $row) {
            $resultAr [$row['user_id']] = $row ['text'];
        }*/
        $dateTimeZone = new \DateTimeZone(getenv('TIMEZONE'));

        try {
            $amocrmUsersResult = Logic::getAmocrmUsers([
                'fields' => ['amocrm_user_id', 'chat_id', 'phone'],
                'group' => 'amocrm_user_id',
                'order' => ['id' => 'DESC'],
            ]);
            $amocrmUsersAr = [];
            foreach ($amocrmUsersResult as $row) {
                $amocrmUsersAr [(int) $row['amocrm_user_id']] = [
                    'chat_id' => (int) $row ['chat_id'],
                    'phone' => $row ['phone']
                ];
            }

            if (!empty($amocrmUsersAr)) {

                $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

                $pipelineId = self::$PIPELINE_ID; // id Воронки

                /** @var \DateTime $startSearch */
                $startSearch = new \DateTime(date('Y-m-d 00:00:00'), $dateTimeZone);
                $startSearch->modify('-' . getenv('AMOCRM_SUCCESS_ORDER_REMINDER_DAYS') . ' days');

                /** @var \DrillCoder\AmoCRM_Wrap\Lead[] $leads */
                $leads = $amo->searchLeads(null, $pipelineId, [], 0, 0, [], $startSearch);

                $leadUsersAr = [];
                /** @inherited $lead */
                foreach ($leads as $lead) {
                    $leadUsersAr [(int)$lead->getMainContactId()] = (int)$lead->getMainContactId();
                }
                $amocrmUsersAr = array_diff_key($amocrmUsersAr, $leadUsersAr);

                if (!empty($amocrmUsersAr)) {
                    $currentDateTime = Queries::now();

                    foreach ($amocrmUsersAr as $amocrmUserId => $chatAr) {
                        Logic::insertCronMessage([
                            'amocrm_user_id' => $amocrmUserId,
                            'amocrm_lead_id' => NULL,
                            'amocrm_status_id' => NULL,
                            'chat_id' => $chatAr ['chat_id'],
                            'phones' => $chatAr ['phone'],
                            'type' => self::REMIND_NO_ORDER,
                            'status' => self::$STATUS_TO_SEND,
                            'created_at' => $currentDateTime,
                            'updated_at' => $currentDateTime,
                        ]);
                    }
                }
            }

            // Update status 14 days messages to every 30 days send
            $remindAgainDays = ('-' . getenv('AMOCRM_SUCCESS_ORDER_AGAIN_REMIND_DAYS') . ' days');
            /** @var \DateTime $startSearch */
            $startSearch = new \DateTime(date('Y-m-d 00:00:00'), $dateTimeZone);
            $startSearch->modify($remindAgainDays);

            $endSearch = new \DateTime(date('Y-m-d 23:59:59'), $dateTimeZone);
            $endSearch->modify($remindAgainDays);

            $messagesAr = Queries::getCronMessageIds($startSearch, $endSearch, self::REMIND_NO_ORDER, [self::$STATUS_SENT, self::$STATUS_REMINDED]);
            if (!empty($messagesAr)) {
                Queries::update('cron_message', [
                    'status' => self::$STATUS_REMIND,
                    'updated_at' => Queries::now(),
                ], [
                    'id' => $messagesAr,
                ]);
            }
        } catch (AmoWrapException $e) {
            TelegramLog::error($e->getMessage());
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