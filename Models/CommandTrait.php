<?php

namespace Models;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\User;

trait CommandTrait {
    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var array
     */
    protected $notes = [];

    /**
     * @var Chat
     */
    protected $chat;

    /**
     * @var User
     */
    protected $user;

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

    public static $PIPELINE_ID = 1979362;
    public static $PIPELINE_NAME = 'Воронка';

    public static $STATUS_TO_SEND = 0;
    public static $STATUS_SENT = 1;
    public static $STATUS_REMIND = 2;
    public static $STATUS_REMINDED = 3;

    public static $AMOCRRM_USER_TYPE_ADMIN = 'admin';
    public static $AMOCRRM_USER_TYPE_MANAGER = 'manager';
    public static $AMOCRRM_USER_TYPE_USER = 'user';

    public static $STATUSES = [
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

    public static $MENU_REQUIRE_CALL = 'Заказать обратный звонок';

    public static $ERROR_PHONE_NOT_FOUND =
        'Ваш номер не найден.' . PHP_EOL .
        'Введите номер, с которого Вы делали заказ.' . PHP_EOL .
        'Для уточнения статуса заказов свяжитесь с менеджером по телефонам:'  . PHP_EOL .
        '%s' . PHP_EOL .
        '%s';

    /*

    const INVITE_LINK = 'https://t.me/joinchat/AAAAAFYbg0VE3uan1UOPfw';

    const ERROR_AMOCRM = 'Ошибка при подключении к хранилищу.';
    const ERROR_PHONE_NOT_FOUND =
        'Ваш номер не найден.' . PHP_EOL .
        'Введите номер, с которого Вы делали заказ.' . PHP_EOL .
        'Для уточнения статуса заказов свяжитесь с менеджером по телефонам:'  . PHP_EOL .
        '%s' . PHP_EOL .
        '%s';
    const SUCCESS_LOGIN = 'Успешная авторизация.';
    const LEADS_NOT_FOUND = 'Сделок не найдено.';

    const MENU_ORDER_STATUS = 'Статус заказа';
    const MENU_HISTORY = 'История заказов';
    const MENU_CATALOG = 'Каталог';
    const MENU_NEWS_CHANNEL = 'Рассказывать о новостях';

    const MENU_REQUIRE_CALL = 'Заказать обратный звонок';*/

    protected function getConversationState() {
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

        $this->chat = $chat;
        $this->user = $user;
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