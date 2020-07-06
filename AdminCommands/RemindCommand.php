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

class RemindCommand extends AdminCommand {
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

    public function execute()
    {
        $message = $this->getMessage();
        $text    = trim($message->getText(true));

        /** @var PDOStatement $pdoStatement */
        $pdoStatement = DB::getPdo()->query('SELECT `user_id`, `text` FROM `message` WHERE `chat_id` = `user_id` AND `entities` LIKE \'%"length":10,"type":"phone_number"%\'', PDO::FETCH_ASSOC);
        $resultAr = [];
        foreach ($pdoStatement as $row) {
            $resultAr [$row['user_id']] = $row ['text'];
        }

        try {
            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

            $pipelineId = 1979362; // id Воронки
            // $statusId = 142; // id Статуса: Успешно реализовано

            $datetime = new \DateTimeImmutable(date('Y-m-d 00:00:00'), new \DateTimeZone('Europe/Kiev'));
            /** @var \DateTime $startSearch */
            $startSearch = $datetime->modify('-' . getenv('AMOCRM_SUCCESS_ORDER_REMINDER_DAYS') . ' days');

            /** @var \DrillCoder\AmoCRM_Wrap\Lead[] $leads */
            $leads = $amo->searchLeads(null, $pipelineId, [], 0, 0, [], $startSearch);

            /** @inherited $lead */
            $leadsAr = [];
            foreach ($leads as $lead) {
                $leadsAr [$lead->getId()] = [
                    'updated_at' => $lead->getDateUpdate(),
                    'phones' => $lead->getPhones(),
                    'status_id' => $lead->getStatusId(),
                ];


                // $leadsAr[] = $lead->getId() . ', ' . $lead->getName() . ': ' . implode(',', $lead->getMainContact()->getPhones());
            }

            TelegramLog::notice(sizeof($leadsAr));
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