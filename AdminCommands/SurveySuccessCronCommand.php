<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Longman\TelegramBot\TelegramLog;
use Models\AdminCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Request;
use PDO;
use PDOStatement;

class SurveySuccessCronCommand extends AdminCommand {
    /**
     * @var string
     */
    protected $name = 'surveysuccesscron';

    /**
     * @var string
     */
    protected $description = 'Create cron for User Survey after success Order.';

    /**
     * @var string
     */
    protected $usage = '/surveysuccesscron';

    public function execute()
    {
        try {
            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

            $pipelineId = self::PIPELINE_ID; // id Воронки
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
                /** @var PDOStatement $pdoStatement */
                $pdoStatement = DB::getPdo()->query('
                SELECT `amocrm_user_id`, `amocrm_lead_id`
                FROM `cron_message`
                WHERE `type` = "' . self::SURVEY_FEEDBACK . '" AND `amocrm_status_id` = ' . $statusId .
                    ' AND `created_at` >= "' . $startSearch->format('Y-m-d H:i:s') . '"' .
                    ' AND `created_at` <= "' . $endSearch->format('Y-m-d H:i:s') . '"'
                    , PDO::FETCH_ASSOC);
                $existUsersAr = [];
                foreach ($pdoStatement as $row) {
                    $existUsersAr [$row ['amocrm_lead_id']] = $row['amocrm_user_id'];
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
                    if (!in_array($leadAr ['user_id'], $existUsersAr)){
                        $sth->execute([
                            ':amocrm_user_id' => $leadAr ['user_id'],
                            ':amocrm_lead_id' => $leadAr ['lead_id'],
                            ':amocrm_status_id' => $leadAr ['status_id'],
                            ':phones' => $this->processPhones($leadAr ['phones']),
                            ':type' => self::SURVEY_FEEDBACK,
                            ':created_at' => $leadAr ['updated_at']->format('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

        } catch (AmoWrapException $e) {
            TelegramLog::error($e->getMessage());
        }
    }
}