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

class SurveySuccessCommand extends AdminCommand {
    /**
     * @var string
     */
    protected $name = 'surveysuccess';

    /**
     * @var string
     */
    protected $description = 'Survey for user after success Order.';

    /**
     * @var string
     */
    protected $usage = '/surveysuccess';

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

    public function execute()
    {
        /** @var PDOStatement $pdoStatement */
        $pdoStatement = DB::getPdo()->query('SELECT `user_id`, `text` FROM `message` WHERE `chat_id` = `user_id` AND `entities` LIKE \'%"length":10,"type":"phone_number"%\'', PDO::FETCH_ASSOC);
        $resultAr = [];
        foreach ($pdoStatement as $row) {
            $resultAr [$row['user_id']] = $row ['text'];
        }

        try {
            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

            $pipelineId = 1979362; // id Воронки
            $statusId = 142; // id Статуса: Успешно реализовано

            /** @var \DateTime $startSearch */
            $startSearch = new \DateTime(date('Y-m-d 00:00:00'), new \DateTimeZone(self::TIMEZONE));
            // Yesterday from 00:00, one Survey for each day
            $startSearch->modify('-1 days');

            $endSearch = new \DateTime(date('Y-m-d 23:59:59'), new \DateTimeZone(self::TIMEZONE));
            $endSearch->modify('-1 days');

            /** @var \DrillCoder\AmoCRM_Wrap\Lead[] $leads */
            $leads = $amo->searchLeads(null, $pipelineId, [$statusId], 0, 0, [], $startSearch);

            /** @inherited $lead */
            $leadsAr = [];
            foreach ($leads as $lead) {
                // Cut today results
                if ($lead->getDateUpdate() <= $endSearch) {
                    // Get last updated Lead for User
                    if (
                        empty($leadsAr [$lead->getMainContactId()]) ||
                            $lead->getDateUpdate() >
                            $leadsAr [$lead->getMainContactId()] ['updated_at']
                        ) {
                        $leadsAr [$lead->getMainContactId()] = [
                            'lead_id' => $lead->getId(),
                            'updated_at' => $lead->getDateUpdate(),
                            'phones' => $lead->getMainContact()->getPhones(),
                            'status_id' => $lead->getStatusId(),
                            'user_id' => $lead->getMainContactId(),
                        ];
                    }
                }
            }

            $sth = DB::getPdo()->prepare('
                INSERT INTO `cron_message` SET 
                `amocrm_user_id` = :amocrm_user_id,
                `amocrm_lead_id` = :amocrm_lead_id,
                `amocrm_status_id` = :amocrm_status_id,
                `chat_id` = NULL,
                `phones` = :phones,
                `type` = :type,
                `status` = 0,
                `created_at` = :created_at,
                `updated_at` = :created_at 
            ');
            foreach ($leadsAr as $userId => $leadAr) {
                $sth->execute([
                    ':amocrm_user_id' => $leadAr ['user_id'],
                    ':amocrm_lead_id' => $leadAr ['lead_id'],
                    ':amocrm_status_id' => $leadAr ['status_id'],
                    ':phones' => $this->processPhones($leadAr ['phones']),
                    ':type' => self::SURVEY_FEEDBACK,
                    ':created_at' => $leadAr ['updated_at']->format('Y-m-d H:i:s'),
                ]);
            }


        } catch (AmoWrapException $e) {
            TelegramLog::error($e->getMessage());
        }
    }

    protected function processPhones($phonesAr) {
        $phonesEscapedAr = [];
        if (!empty($phonesAr)) {
            foreach ($phonesAr as $phoneNum) {
                $phonesEscapedAr [] = substr(preg_replace('/[^0-9+]/', '', $phoneNum), -10);
            }
        }
        if (empty($phonesEscapedAr)) {
            return '';
        }
        return implode(',', $phonesEscapedAr);
    }
}