<?php

namespace Models;

use Longman\TelegramBot\TelegramLog;

class Logic {

    public static function getAmocrmUsers($options) {
        $default = [
            'fields' => ['*'],
            'filters' => [],
            'order' => ['updated_at' => 'DESC'],
            'group' => [],
            'limit' => FALSE,
        ];
        $options = array_merge($default, $options);

        /** @var array $result */
        $result = Queries::select('amocrm_user', [
            'fields' => $options ['fields'],
            'where' => $options ['filters'],
            'order' => $options ['order'],
            'limit' => $options ['limit'],
            'group' => $options ['group'],
        ]);

        return $result;
    }

    protected static function getCronMessagesFilters($options) {
        if (is_string($options ['type'])) {
            $filters [] = ['type' => $options ['type']];
        }
        if (array_key_exists('amocrm_status_id', $options)) {
            $filters [] = ['amocrm_status_id' => $options ['amocrm_status_id']];
        }
        if ($options ['fromDateTime'] instanceof \DateTime) {
            $filters [] = ['created_at', $options ['fromDateTime']->format('Y-m-d H:i:s'), '>='];
        }
        if ($options ['toDateTime'] instanceof \DateTime) {
            $filters [] = ['created_at', $options ['toDateTime']->format('Y-m-d H:i:s'), '<='];
        }
        if (!empty($options ['status'])) {
            $filters [] = ['status' => $options ['status']];
        }

        return $filters;
    }

    public static function getCronMessages(array $fields = ['*'], $options = []) {
        $default = [
            'type' => false,
            'fromDateTime' => false,
            'toDateTime' => false,
            'status' => [],
        ];
        $options = array_merge($default, $options);

        $filters = self::getCronMessagesFilters($options);

        /** @var \PDOStatement $sth */
        $sth = Queries::select('cron_message', [
            'fields' => $fields,
            'filters' => $filters,
            'sth' => true,
        ]);
        $cronMessages = $sth->fetchAll();

        return $cronMessages;
    }

    public static function getCronMessagesColumn(string $columnName, array $options = []) {
        $default = [
            'type' => false,
            'fromDateTime' => false,
            'toDateTime' => false,
            'status' => [],
        ];
        $options = array_merge($default, $options);

        $filters = self::getCronMessagesFilters($options);

        /** @var \PDOStatement $sth */
        $sth = Queries::select('cron_message', [
            'fields' => $columnName,
            'filters' => $filters,
            'sth' => true,
        ]);
        $cronMessages = $sth->fetchColumn();

        return $cronMessages;
    }

    /**
     * @param array $data
     * @param array $where
     *
     * @return bool
     * @throws \Exception
     */
    public static function updateCronMessage(array $data, array $where = []) {
        return BasePdo::update('cron_message', $data, $where);
    }

    public static function insertCronMessage(array $data) {
        BasePdo::insert('cron_message', $data);
    }

    /**
     * @param array $data
     * @param array $where
     *
     * @return bool
     * @throws \Exception
     */
    public static function updateAmocrmUser(array $data, array $where = []) {
        return BasePdo::update('amocrm_user', $data, $where);
    }
}
