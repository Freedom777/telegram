<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Longman\TelegramBot\TelegramLog;
use Models\AdminCommand;
use Models\Logic;

class SurveyFailCronCommand extends AdminCommand {
    /**
     * @var string
     */
    protected $name = 'surveyfailcron';

    /**
     * @var string
     */
    protected $description = 'Create cron for User Survey after fail Order.';

    /**
     * @var string
     */
    protected $usage = '/surveyfailcron';

    public function execute()
    {
        try {
            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

            $pipelineId = self::$PIPELINE_ID; // id Воронки
            $statusId = 143; // id Статуса: Закрыто и не реализовано

            /** @var \DateTime $startSearch */
            $startSearch = new \DateTime(date('Y-m-d 00:00:00'), new \DateTimeZone(getenv('TIMEZONE')));
            // Yesterday from 00:00, one Survey for each day
            $startSearch->modify('-1 days');

            $endSearch = new \DateTime(date('Y-m-d 23:59:59'), new \DateTimeZone(getenv('TIMEZONE')));
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
                            'user_id' => $lead->getMainContactId(),
                            'lead_id' => $lead->getId(),
                            'status_id' => $lead->getStatusId(),
                            'updated_at' => $lead->getDateUpdate(),
                            'phones' => $lead->getMainContact()->getPhones(),
                        ];
                    }
                }
            }

            if (!empty($leadsAr)) {
                $cronMessages = Logic::getCronMessages(['amocrm_user_id', 'amocrm_lead_id'], [
                    'type' => self::SURVEY_NOT_BOUGHT,
                    'amocrm_status_id' => $statusId,
                    'fromDateTime' => $startSearch,
                    'toDateTime' => $endSearch,
                ]);

                $existUsersAr = array_column($cronMessages, 'amocrm_user_id', 'amocrm_lead_id');

                foreach ($leadsAr as $userId => $leadAr) {
                    if (!in_array($leadAr ['user_id'], $existUsersAr)) {
                        Logic::insertCronMessage([
                            'amocrm_user_id' => $leadAr ['user_id'],
                            'amocrm_lead_id' => $leadAr ['lead_id'],
                            'amocrm_status_id' => $leadAr ['status_id'],
                            'chat_id' => NULL,
                            'phones' => $this->processPhones($leadAr ['phones']),
                            'type' => self::SURVEY_NOT_BOUGHT,
                            'status' => self::$STATUS_TO_SEND,
                            'created_at' => $leadAr ['updated_at']->format('Y-m-d H:i:s'),
                            'updated_at' => $leadAr ['updated_at']->format('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

        } catch (AmoWrapException $e) {
            TelegramLog::error($e->getMessage());
        }
    }
}