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

class BillSentCommand extends AdminCommand {
    /**
     * @var string
     */
    protected $name = 'remind';

    /**
     * @var string
     */
    protected $description = 'Remind user about events.';

    /**
     * @var string
     */
    protected $usage = '/remind';

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
        $message = $this->getMessage();
        $text    = trim($message->getText(true));

        try {
            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

            $pipelineId = 1979362; // id Воронки
            $statusId = 29361433; // id Статуса: Договор/счет отправлен

            /** @var \DateTime $startSearch */
            $startSearch = new \DateTime(date('Y-m-d 00:00:00'), new \DateTimeZone(self::TIMEZONE));
            $startSearch->modify('-' . getenv('AMOCRM_BILL_SENT_REMINDER_DAYS') . ' days');

            $endSearch = new \DateTime(date('Y-m-d 23:59:59'), new \DateTimeZone(self::TIMEZONE));
            $endSearch->modify('-' . getenv('AMOCRM_BILL_SENT_REMINDER_DAYS') . ' days');

            /** @var \DrillCoder\AmoCRM_Wrap\Lead[] $leads */
            $leads = $amo->searchLeads(null, $pipelineId, [], 0, 0, [], $startSearch);

            /** @inherited $lead */
            $leadsAr = [];
            foreach ($leads as $lead) {
                // Cut -AMOCRM_SUCCESS_ORDER_REMINDER_DAYS day results with Order sent status
                if ($lead->getDateUpdate() <= $endSearch && $lead->getStatusId() == $statusId) {
                    $leadsAr [$lead->getId()] = [
                        'user_id' => $lead->getMainContactId(),
                        'lead_id' => $lead->getId(),
                        'status_id' => $lead->getStatusId(),
                        'updated_at' => $lead->getDateUpdate(),
                        'phones' => $lead->getMainContact()->getPhones(),
                    ];
                }
            }

            if (!empty($leadsAr)) {
                // Check leads, remove from process if status changed
                foreach ($leads as $lead) {
                    if (isset($leadsAr [$lead->getId()]) &&
                        $lead->getDateUpdate() > $leadsAr [$lead->getId()] ['updated_at'] &&
                        $lead->getStatusId() != $statusId
                    ) {
                        unset($leadsAr [$lead->getId()]);
                    }
                }

                if (!empty($leadsAr)) {
                    /** @var PDOStatement $pdoStatement */
                    $pdoStatement = DB::getPdo()->query('
                        SELECT `amocrm_user_id`, `amocrm_lead_id`
                        FROM `cron_message`
                        WHERE `type` = "' . self::BILL_SENT . '" AND `amocrm_status_id` = ' . $statusId .
                                ' AND `created_at` >= "' . $startSearch->format('Y-m-d H:i:s') . '"' .
                                ' AND `created_at` <= "' . $endSearch->format('Y-m-d H:i:s') . '"'
                                , PDO::FETCH_ASSOC);
                    $existLeadsAr = [];
                    foreach ($pdoStatement as $row) {
                        $existLeadsAr [] = $row ['amocrm_lead_id'];
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
                        if (!in_array($leadAr ['lead_id'], $existLeadsAr)){
                            $sth->execute([
                                ':amocrm_user_id' => $leadAr ['user_id'],
                                ':amocrm_lead_id' => $leadAr ['lead_id'],
                                ':amocrm_status_id' => $leadAr ['status_id'],
                                ':phones' => $this->processPhones($leadAr ['phones']),
                                ':type' => self::BILL_SENT,
                                ':created_at' => $leadAr ['updated_at']->format('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
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