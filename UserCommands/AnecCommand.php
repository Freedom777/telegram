<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Request;

/**
 * Anecdot command
 *
 * Get Anecdot
 */
class AnecCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'anec';

    /**
     * @var string
     */
    protected $description = 'Anecdot command';

    /**
     * @var string
     */
    protected $usage = '/anec';

    /**
     * @var string
     */
    protected $version = '1.0';

    /**
     * @var bool
     */
    protected $private_only = false;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        $dataFull = file_get_contents('https://www.anekdot.ru/random/anekdot/');
        preg_match_all('#' .
            '<div class="topicbox" id="' . '(\d+)' . '"' .
            '.*?' .
            '<div class="text">(.*?)</div>' .
            '#uis', $dataFull, $matches);

        $text = 'Ошибка в получении анекдотов.';
        if (!empty($matches[1]) && !empty($matches[2])) {
            $newDataAr = array_combine($matches[1], $matches[2]);
            $textAr = [];
            $pdo = DB::getPdo();

            $sql = 'SELECT `id`, `description` FROM `anecdot` WHERE `id` IN (' . implode(',', array_keys($newDataAr)) . ')';
            $existRecs = $pdo->query($sql, \PDO::FETCH_ASSOC);
            foreach ($existRecs as $existRec) {
                $textAr [$existRec ['id']] = $existRec ['description'];
                unset($newDataAr[$existRec ['id']]);
            }

            if (!empty($newDataAr)) {
                foreach ($newDataAr as $id => $value) {
                    $str = str_replace(['<br>', '&quot;'], [PHP_EOL, '"'], $value);
                    $stmt = $pdo->prepare('INSERT INTO `anecdot` SET `id` = ?, `description` = ?');
                    $stmt->execute([$id, $str]);
                    $textAr [$id] = $str;
                }
            }

            $text = 'Источник: www.anekdot.ru' . PHP_EOL . PHP_EOL . implode(PHP_EOL . PHP_EOL, $textAr);
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
        ];

        return Request::sendMessage($data);
    }
}
