<?php

namespace Models;

class Queries extends BasePdo {
    public static function getAmocrmUserByChatId($chatId, $fields = ['*']) {
        /** @var \PDOStatement $pdoStatement */
        $sth = self::select('amocrm_user', [
            'fields' => $fields,
            'where' => [
                'chat_id' => $chatId,
            ],
            'order' => ['updated_at' => 'DESC'],
            'sth' => true,
            'limit' => 1,
        ]);
        $amocrmUser = $sth->fetch(\PDO::FETCH_ASSOC);

        return $amocrmUser;
    }

    public static function getCronMessageIds(\DateTime $fromDateTime, \DateTime $toDateTime, string $type, array $statuses = []) {
        /** @var \PDOStatement $pdoStatement */
        $sth = self::select('cron_message', [
            'fields' => 'id',
            'where' => [
                'type' => $type,
                'amocrm_status_id' => null,
                'status' => $statuses,
                ['created_at', $fromDateTime->format('Y-m-d H:i:s'), '>='],
                ['created_at', $toDateTime->format('Y-m-d H:i:s'), '<='],
            ],
            'sth' => true,
        ]);
        $cronIds = $sth->fetchColumn();

        return $cronIds;
    }

    public static function now() {
        return (new \DateTime())->format('Y-m-d H:i:s');
    }
}