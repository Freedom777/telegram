<?php

namespace Models;

use Longman\TelegramBot\Commands\UserCommand as UserCommandBase;
use Longman\TelegramBot\DB;

abstract class UserCommand extends UserCommandBase {
    use CommandTrait;

    const ERROR_AMOCRM = 'Ошибка при подключении к хранилищу.';
    const SUCCESS_LOGIN = 'Успешная авторизация.';
    const LEADS_NOT_FOUND = 'Сделок не найдено.';

    const MENU_ORDER_STATUS = 'Статус заказа';
    const MENU_HISTORY = 'История заказов';
    const MENU_CATALOG = 'Каталог';
    const MENU_NEWS_CHANNEL = 'Рассказывать о новостях';

    const MENU_REQUIRE_CALL = 'Заказать обратный звонок';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    protected function getAmocrmUserIdByPhone($phone) {
        $exist = Logic::getAmocrmUsers([
            'fields' => ['id', 'amocrm_user_id'],
            'filters' => [
                'chat_id' => $this->chat_id,
                'phone' => $phone,
                'limit' => 1,
            ]
        ]);

        if (!empty($exist)) {
            $currentDateTime = BasePdo::now();
            $updateSuccess = Logic::updateAmocrmUser([
                'updated_at' => $currentDateTime
            ], [
                'id' => $exist[0]['id'],
            ]);

            return $updateSuccess;
        }

        return false;
    }

    protected function checkInsertUser($phone, $amocrmUserId) {
        // Update table
        if ($this->user_id == $this->chat_id) { // Private chat
            $currentDateTime = BasePdo::now();

            if (null === $amocrmUserId) {
                // Contact in AMOCRM not found
                $amocrmUsers = Logic::getAmocrmUsers([
                    'fields' => 'id',
                    'filters' => ['phone' => $phone],
                    'limit' => 1
                ]);

                if (empty($amocrmUsers)) {
                    Logic::insertAmocrmUser([
                        'chat_id' => $this->user_id,
                        'amocrm_user_id' => null,
                        'phone' => $phone,
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime
                    ]);
                } else {
                    Logic::updateAmocrmUser([
                        'chat_id' => $this->user_id,
                        'amocrm_user_id' => null,
                        'updated_at' => $currentDateTime,
                    ], [
                        'id' => $amocrmUsers [0] ['id'],
                    ]);
                }
            } else {
                $amocrmUsers = Logic::getAmocrmUsers([
                    'fields' => 'phone',
                    'filters' => ['chat_id' => $this->user_id, 'amocrm_user_id' => $amocrmUserId],
                    'limit' => 1
                ]);
                if (empty($amocrmUsers) || $phone != $amocrmUsers [0] ['phone']) {
                    Logic::insertAmocrmUser([
                        'chat_id' => $this->user_id,
                        'amocrm_user_id' => $amocrmUserId,
                        'phone' => $phone,
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime
                    ]);
                }
            }
        }
    }

    protected function getContactIdByChatId($chatId) {
        $amocrmUser = Queries::getAmocrmUserByChatId($chatId, 'amocrm_user_id');
        if (!empty($amocrmUser) && !empty($amocrmUser ['amocrm_user_id'])) {
            return $amocrmUser ['amocrm_user_id'];
        }

        return false;
    }
}