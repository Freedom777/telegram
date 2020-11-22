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

    protected function getAmocrmUserIdByPhone($phone) {
        /** @var \PDOStatement $pdoStatement */
        $sth = DB::getPdo()->prepare('
                                        SELECT `id`, `amocrm_user_id`
                                        FROM `amocrm_user`
                                        WHERE `chat_id` = :chat_id AND `phone` = :phone
                                        ORDER BY `id` DESC
                                        LIMIT 1'
        );
        $sth->execute([
            ':chat_id' => $this->chat_id,
            ':phone' => $phone,
        ]);
        $exist = $sth->fetch(\PDO::FETCH_ASSOC);

        if (!empty($exist) && !empty($exist['amocrm_user_id'])) {
            $currentDateTime = date('Y-m-d H:i:s');
            $sth = DB::getPdo()->prepare('
                                        UPDATE `amocrm_user` SET
                                        `updated_at` = :current_date_time
                                        WHERE `id` = :id
                                    ');
            $sth->execute([
                ':id' => $exist ['id'],
                ':current_date_time' => $currentDateTime
            ]);

            return $exist ['amocrm_user_id'];
        }

        return null;
    }

    protected function checkInsertUser($phone, $amocrmUserId) {
        // Update table
        if ($this->user_id == $this->chat_id) { // Private chat
            $currentDateTime = date('Y-m-d H:i:s');
            if (null === $amocrmUserId) {
                // Contact in AMOCRM not found
                $sth = DB::getPdo()->prepare('
                                    SELECT `id`
                                    FROM `amocrm_user`
                                    WHERE `phone` = :phone
                                    ORDER BY `id` DESC
                                    LIMIT 1'
                );

                $sth->execute([
                    ':phone' => $phone,
                ]);
                $exist = $sth->fetch(\PDO::FETCH_ASSOC);
                if (empty($exist)) {
                    $this->insertUser(null, $phone);
                } else {
                    $sth = DB::getPdo()->prepare('
                                        UPDATE `amocrm_user` SET
                                        `chat_id` = :chat_id,
                                        `amocrm_user_id` = :amocrm_user_id,
                                        `updated_at` = :current_date_time
                                        WHERE `id` = :id
                                    ');
                    $sth->execute([
                        ':id' => $exist ['id'],
                        ':chat_id' => $this->chat_id,
                        ':amocrm_user_id' => null,
                        ':current_date_time' => $currentDateTime
                    ]);
                }
            } else {
                /** @var \PDOStatement $pdoStatement */
                $sth = DB::getPdo()->prepare('
                                        SELECT `phone`
                                        FROM `amocrm_user`
                                        WHERE `chat_id` = :chat_id AND `amocrm_user_id` = :amocrm_user_id
                                        ORDER BY `id` DESC
                                        LIMIT 1'
                );
                $sth->execute([
                    ':chat_id' => $this->chat_id,
                    ':amocrm_user_id' => $amocrmUserId,
                ]);
                $exist = $sth->fetch(\PDO::FETCH_ASSOC);
                if (empty($exist) || $phone != $exist ['phone']) {
                    $this->insertUser($amocrmUserId, $phone);
                }
            }
        }
    }

    protected function insertUser($amocrmUserId, $phone) {
        $currentDateTime = date('Y-m-d H:i:s');

        $sth = DB::getPdo()->prepare('
                                            INSERT INTO `amocrm_user` SET
                                            `chat_id` = :chat_id, 
                                            `amocrm_user_id` = :amocrm_user_id,
                                            `phone` = :phone,
                                            `created_at` = :current_date_time,
                                            `updated_at` = :current_date_time
                                        ');
        $sth->execute([
            ':chat_id' => $this->chat_id,
            ':amocrm_user_id' => $amocrmUserId,
            ':phone' => $phone,
            ':current_date_time' => $currentDateTime
        ]);
    }

    /**
     * @param int $chatId
     *
     * @return false|string
     */
    protected function getUserPhone($chatId) {
        $amocrmUser = Queries::getAmocrmUserByChatId($chatId, 'phone');
        if (!empty($amocrmUser) && !empty($amocrmUser ['phone'])) {
            return $amocrmUser ['phone'];
        }

        return false;
    }

    protected function getContactIdByChatId($chatId) {
        $amocrmUser = Queries::getAmocrmUserByChatId($chatId, 'amocrm_user_id');
        if (!empty($amocrmUser) && !empty($amocrmUser ['amocrm_user_id'])) {
            return $amocrmUser ['amocrm_user_id'];
        }

        return false;
    }
}