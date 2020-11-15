<?php

namespace Models;

use Longman\TelegramBot\DB;

class Queries {
    public static function getAmocrmUserByChatId($chatId) {
        $sql = '
                SELECT *
                FROM `amocrm_user`
                WHERE `chat_id` = :chat_id
                ORDER BY `updated_at` DESC
                LIMIT 1
        ';
        /** @var \PDOStatement $pdoStatement */
        $sth = DB::getPdo()->prepare($sql);
        $sth->execute(['chat_id' => $chatId]);
        $amocrmUser = $sth->fetch(\PDO::FETCH_ASSOC);

        return $amocrmUser;
    }

    public static function getCronMessageIds(\DateTime $fromDateTime, \DateTime $toDateTime, string $type, array $statuses = []) {
        $whereIn = '';
        if (!empty($statuses)) {
            $params = array_combine(
                array_map(function($i){ return ':statusId'.$i; }, array_keys($statuses)),
                $statuses
            );
            $whereIn = 'AND ' . '`status` IN (' . implode(',', array_keys($statuses)) . ')';
        }
        $params = array_merge($params, [
            'type' => $type,
            'start_search' => $fromDateTime->format('Y-m-d H:i:s'),
            'end_search' => $toDateTime->format('Y-m-d H:i:s'),
        ]);

        $sql = '
            SELECT `id` FROM  `cron_message`
            WHERE `type` = :type
              AND `amocrm_status_id` IS NULL
              ' . $whereIn . '
              AND `created_at` >= :start_search 
              AND `created_at` <= :end_search
        ';

        return $params;
        /** @var \PDOStatement $pdoStatement */
        $sth = DB::getPdo()->prepare($sql);
        $sth->execute($params);
        $cronIds = $sth->fetchColumn();

        return $cronIds;
    }

    public static function getSql($string,$data) {
        $indexed = ($data == array_values($data));
        foreach($data as $k=>$v) {
            if (is_string($v)) {
                $v = '"' . $v . '"';
            }
            if ($indexed) {
                $string=preg_replace('/\?/', $v, $string, 1);
            } else {
                $string=str_replace(':' . $k, $v, $string);
            }
        }
        return $string;
    }

}