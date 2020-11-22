<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Longman\TelegramBot\TelegramLog;
use Models\AdminCommand;
use Longman\TelegramBot\DB;
use PDO;
use PDOStatement;

class BillSentCronCommand extends AdminCommand
{
    /**
     * @var string
     */
    protected $name = 'billsentcron';

    /**
     * @var string
     */
    protected $description = 'Create cron for remind user about Bill sent event.';

    /**
     * @var string
     */
    protected $usage = '/billsentcron';

    public function execute()
    {
        $message = $this->getMessage();
        $text = trim($message->getText(true));
        $dateTimeZone = new \DateTimeZone(getenv('TIMEZONE'));
        $pipelineId = self::$PIPELINE_ID; // id Воронки
        $statusId = 29361433; // id Статуса: Договор/счет отправлен

        try {
            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

            $reminderDays = '-' . getenv('AMOCRM_BILL_SENT_REMINDER_DAYS') . ' days';
            /** @var \DateTime $startSearch */
            $startSearch = new \DateTime(date('Y-m-d 00:00:00'), $dateTimeZone);
            $startSearch->modify($reminderDays);

            $endSearch = new \DateTime(date('Y-m-d 23:59:59'), $dateTimeZone);
            $endSearch->modify($reminderDays);

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
                        `status` = ' . self::$STATUS_TO_SEND . ',
                        `created_at` = :created_at,
                        `updated_at` = :created_at 
                    ');
                    foreach ($leadsAr as $userId => $leadAr) {
                        if (!in_array($leadAr ['lead_id'], $existLeadsAr)) {
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

            // Update status 3 days messages to 10 days expiring
            $remindAgainDays = ('-' . getenv('AMOCRM_BILL_SENT_AGAIN_REMIND_DAYS') - getenv('AMOCRM_BILL_SENT_REMINDER_DAYS') . ' days');
            /** @var \DateTime $startSearch */
            $startSearch = new \DateTime(date('Y-m-d 00:00:00'), $dateTimeZone);
            $startSearch->modify($remindAgainDays);

            $endSearch = new \DateTime(date('Y-m-d 23:59:59'), $dateTimeZone);
            $endSearch->modify($remindAgainDays);

            $pdoStatement = DB::getPdo()->query('
                SELECT `id` FROM  `cron_message`
                WHERE `type` = "' . self::BILL_SENT . '"
                  AND `amocrm_status_id` = ' . $statusId .
                ' AND `status` = ' . self::$STATUS_SENT .
                ' AND `created_at` >= "' . $startSearch->format('Y-m-d H:i:s') . '"' .
                ' AND `created_at` <= "' . $endSearch->format('Y-m-d H:i:s') . '"'
                , PDO::FETCH_ASSOC);

            $messagesAr = [];
            foreach ($pdoStatement as $row) {
                $messagesAr [] = $row ['id'];
            }
            if (!empty($messagesAr)) {
                $sth = DB::getPdo()->prepare('
                    UPDATE `cron_message` SET 
                        `status` = ' . self::$STATUS_REMIND . ',
                        `updated_at` = NOW()
                    WHERE `id` IN (' . implode(',', $messagesAr) . ')
                ');
                $sth->execute($sth);
            }

        } catch (AmoWrapException $e) {
            TelegramLog::error($e->getMessage());
        }
    }
}