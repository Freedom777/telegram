<?php

namespace Models;

use Longman\TelegramBot\Commands\AdminCommand as AdminCommandBase;
use Longman\TelegramBot\Conversation;

abstract class AdminCommand extends AdminCommandBase {
    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var array
     */
    protected $notes = [];

    /**
     * @var int
     */
    protected $chat_id;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $text;

    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

    /**
     * @var bool
     */
    protected $private_only = true;

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

    const PIPELINE_ID = 1979362;
    const PIPELINE_NAME = 'Воронка';

    const STATUS_TO_SEND = 0;
    const STATUS_SENT = 1;
    const STATUS_REMIND = 2;
    const STATUS_REMINDED = 3;

    const MESSAGE_GET_CALL = 'Обратный звонок';
    const MENU_REQUIRE_CALL = 'Заказать обратный звонок';
    const ERROR_PHONE_NOT_FOUND =
        'Ваш номер не найден.' . PHP_EOL .
        'Введите номер, с которого Вы делали заказ.' . PHP_EOL .
        'Для уточнения статуса заказов свяжитесь с менеджером по телефонам:'  . PHP_EOL .
        '%s' . PHP_EOL .
        '%s';

    protected function getState() {
        //Conversation start
        $this->conversation = new Conversation($this->user_id, $this->chat_id, $this->getName());

        $this->notes = &$this->conversation->notes;
        !is_array($this->notes) && $this->notes = [];

        //cache data from the tracking session if any
        $state = 0;
        if (isset($this->notes ['state'])) {
            $state = $this->notes ['state'];
        }

        return $state;
    }

    protected function prepareInput() {
        $message = $this->getMessage();
        $chat    = $message->getChat();
        $user    = $message->getFrom();

        $this->chat_id = $chat->getId();
        $this->user_id = $user->getId();
        $this->text = trim($message->getText(true));

        // Default Response data
        return [
            'chat_id' => $this->chat_id,
        ];
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