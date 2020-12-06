<?php

namespace Models;

use Longman\TelegramBot\DB;
use Longman\TelegramBot\TelegramLog;

class BasePdo {
    public static function now() {
        $dateTimeZone = new \DateTimeZone(getenv('TIMEZONE'));

        return (new \DateTime('now', $dateTimeZone))->format('Y-m-d H:i:s');
    }

    public static function getSql($string,$data) {
        $indexed = ($data == array_values($data));
        foreach($data as $k=>$v) {
            if (is_string($v)) {
                $v = '"' . $v . '"';
            }
            if ($indexed) {
                $string = preg_replace('/\?/', $v, $string, 1);
            } else {
                $string = str_replace(':' . $k, $v, $string);
            }
        }
        return $string;
    }

    /**
     * @param string $operator INSERT|UPDATE
     * @param string $table
     * @param array $data
     *
     * @return string
     * @throws \Exception
     */
    protected static function prepareInsertUpdateSet(string $operator, string $table, array $data) {
        if (empty($data)) {
            throw new \Exception('Empty data.');
        }
        $sql = '';
        $operator = strtoupper($operator);
        if ('INSERT' == $operator) {
            $sql .= 'INSERT INTO ';
        } elseif ('UPDATE' == $operator) {
            $sql .= 'UPOATE ';
        } else {
            throw new \Exception('Invalid operator.');
        }
        $sql .= '`' . $table . '` SET' . PHP_EOL;
        $dataKeys = [];
        foreach ($data as $key => $value) {
            $dataKeys [] = '`' . $key . '`' . ' = ' . ':' . $key;
        }
        $sql .= implode(',' . PHP_EOL, $dataKeys);

        return $sql;
    }

    /**
     * @param string $table
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public static function insert(string $table, array $data = []) {
        $sql = self::prepareInsertUpdateSet('insert', $table, $data);

        /** @var \PDOStatement $pdoStatement */
        $sth = DB::getPdo()->prepare($sql);
        self::bindArrayValue($sth, $data);

        $result = $sth->execute();

        return $result;
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     *
     * @return bool
     * @throws \Exception
     */
    public static function update(string $table, array $data = [], $where = []) {
        $sql = self::prepareInsertUpdateSet('update', $table, $data);
        $sql .= self::processWhere($data, $where);

        /** @var \PDOStatement $pdoStatement */
        $sth = DB::getPdo()->prepare($sql);
        self::bindArrayValue($sth, $data);

        $result = $sth->execute();

        return $result;
    }

    protected static function bindArrayValue($sth, $data, $typeArray = false) {
        if (is_object($sth) && ($sth instanceof \PDOStatement)) {
            foreach($data as $key => $value) {
                if ($typeArray) {
                    $sth->bindValue(':' . $key, $value, $typeArray[$key]);
                } else {
                    switch (gettype($value)) {
                        case 'int':
                            $param = \PDO::PARAM_INT;
                            break;
                        case 'bool':
                            $param = \PDO::PARAM_BOOL;
                            break;
                        case 'NULL':
                            $param = \PDO::PARAM_NULL;
                            break;
                        case 'string':
                            $param = \PDO::PARAM_STR;
                            break;
                        default:
                            $param = false;
                            break;
                    }
                    if (false !== $param) {
                        $sth->bindValue(':' . $key, $value, $param);
                    }
                }
            }
        }
    }

    protected static function processWhere(&$bindings, $whereOptions = []) {
        $sql = '';
        $whereOptions = (array) $whereOptions;

        if (!empty($whereOptions)) {
            $whereCond = [];
            $idx = 0;
            foreach ($whereOptions as $field => $value) {
                ++$idx;
                $operator = '=';
                if (is_array($field)) {
                    $fieldName = $field [0];
                    $val = $field [1];
                    if (!empty($field[2])) {
                        $operator =  $field[2];
                    }
                } else {
                    $fieldName = $field;
                    $val = $value;
                }

                if (is_array($val) && !empty($val)) {
                    $bindingsWhere = array_combine(
                        array_map(function($i) use ($idx){ return 'wherein'.$idx.'_'.$i; }, array_keys($val)),
                        $val
                    );
                    $valueBindings = array_keys($bindingsWhere);
                    array_walk($valueBindings, function (&$item) {
                        return ':' . $item;
                    });
                    $bindings = array_merge($bindings, $bindingsWhere);
                    $whereCond [] = '`' . $fieldName . '`' . ' IN (' . implode('\', \'', $valueBindings) . ')';
                } else {
                    if (null === $val) {
                        $whereCond [] = '`' . $fieldName . '`' . ' IS NULL';
                    } else {
                        $bindings = array_merge($bindings, [$fieldName . $idx => $val]);
                        $whereCond [] = '`' . $fieldName . '`' . ' ' . $operator . ' ' . ':' . $fieldName . $idx;
                    }
                }
            }

            if (!empty($whereCond)) {
                $sql = 'WHERE ' . implode(PHP_EOL . 'AND ', $whereCond) . PHP_EOL;
            }
        }

        return $sql;
    }

    /**
     * @param string $table
     * @param array $options
     *
     * @return array|bool|\PDOStatement
     */
    public static function select(string $table, array $options = []) {
        $default = [
            'fields' => ['*'],
            'where' => [],
            'group' => [],
            'order' => [],
            'limit' => false,
            'index' => false,
            'sth' => false,
        ];
        $options = array_merge($default, $options);
        $bindings = [];

        // SELECT ... FROM ...
        $options ['fields'] = (array) $options ['fields'];
        $fields = $options ['fields'];
        array_walk($fields, function (&$item) {
            if (is_string($item) && $item != '*') {
                $item = '`' . $item . '`';
            }
            return $item;
        });
        $selectFields = implode(', ', $fields);
        $sql = 'SELECT ' . $selectFields . ' FROM `' . $table . '`' . PHP_EOL;

        // WHERE ...
        $sql .= self::processWhere($bindings, $options ['where']);

        // GROUP BY ...
        if (!empty($options ['group'])) {
            $options ['group'] = (array) $options ['group'];
            array_walk($options ['group'], function (&$item) {
                return '`' . $item . '`';
            });
            $sql .= 'GROUP BY ' . implode(', ', $options ['group']) . PHP_EOL;
        }

        // ORDER BY ...
        if (!empty($options ['order'])) {
            $order = [];
            $options ['order'] = (array) $options ['order'];
            foreach ($options ['order'] as $field => $sortOrder) {
                if (is_numeric($field)) {
                    list($sortField, $sortOrder) = explode(' ', $sortOrder);
                    $order [] = '`' . $sortField . '`' . ' ' . !empty($sortOrder) ? $sortOrder : 'ASC';
                } else {
                    $order [] = '`' . $field . '`' . ' ' . $sortOrder;
                }
            }
            if (!empty($order)) {
                $sql .= 'ORDER BY ' . implode(', ', $order) . PHP_EOL;
            }
        }

        // LIMIT ...
        if (!empty($options ['limit'])) {
            $sql .= 'LIMIT ' . $options ['limit'] . PHP_EOL;
        }
        TelegramLog::error($sql);
        TelegramLog::error(var_export($bindings, true));

        // Result prepare
        /** @var \PDOStatement $pdoStatement */
        $sth = DB::getPdo()->prepare($sql);
        self::bindArrayValue($sth, $bindings);

        $sth->execute();
        if ($options ['sth']) {
            return $sth;
        }
        $result = [];
        if (!empty($options ['index']) && (in_array('*', $options ['fields']) || in_array($options ['index'], $options ['fields']))) {
            while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $result[$options ['index']] = $row;
            }
        } else {
            while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }
        }

        return $result;
    }
}
