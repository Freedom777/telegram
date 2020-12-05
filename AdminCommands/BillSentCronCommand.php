<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Longman\TelegramBot\TelegramLog;
use Models\AdminCommand;
use Longman\TelegramBot\DB;
use Models\BasePdo;
use Models\Logic;
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

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse|void
     * @throws \Exception
     */
    public function execute()
    {
        $dateTimeZone = new \DateTimeZone(getenv('TIMEZONE'));
        $pipelineId = self::$PIPELINE_ID; // id Воронки
        $amocrmStatusId = 29361433; // id Статуса: Договор/счет отправлен

        try {
            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

            $reminderDays = '-' . getenv('AMOCRM_BILL_SENT_REMINDER_DAYS') . ' days';

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
                if ($lead->getDateUpdate() <= $endSearch && $lead->getStatusId() == $amocrmStatusId) {
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
                        $lead->getStatusId() != $amocrmStatusId
                    ) {
                        unset($leadsAr [$lead->getId()]);
                    }
                }

                if (!empty($leadsAr)) {
                    /** @var array $existLeadsAr */
                    $existLeadsAr = Logic::getCronMessagesColumn('amocrm_lead_id', [
                        'type' => self::BILL_SENT,
                        'amocrm_status_id' => $amocrmStatusId,
                        'fromDateTime' => $startSearch,
                        'toDateTime' => $endSearch,
                    ]);

                    foreach ($leadsAr as $userId => $leadAr) {
                        if (!in_array($leadAr ['lead_id'], $existLeadsAr)) {
                            BasePdo::insert('cron_message', [
                                'amocrm_user_id' => $leadAr ['user_id'],
                                'amocrm_lead_id' => $leadAr ['lead_id'],
                                'amocrm_status_id' => $leadAr ['status_id'],
                                'phones' => $this->processPhones($leadAr ['phones']),
                                'type' => self::BILL_SENT,
                                'created_at' => $leadAr ['updated_at']->format('Y-m-d H:i:s'),
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

            $messagesAr = Logic::getCronMessagesColumn('amocrm_lead_id', [
                'type' => self::BILL_SENT,
                'amocrm_status_id' => $amocrmStatusId,
                'status' => self::$STATUS_SENT,
                'fromDateTime' => $startSearch,
                'toDateTime' => $endSearch,
            ]);

            if (!empty($messagesAr)) {
                Logic::updateCronMessage([
                    'status' => self::$STATUS_REMIND,
                    'updated_at' => BasePdo::now(),
                ], [
                    'id' => $messagesAr,
                ]);
            }

        } catch (AmoWrapException $e) {
            TelegramLog::error($e->getMessage());
        }
    }
}