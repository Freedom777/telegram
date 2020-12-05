<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * Corona command
 *
 * Get Coronavirus statistics
 */
class CoronaCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'corona';

    /**
     * @var string
     */
    protected $description = 'Coronavirus command';

    /**
     * @var string
     */
    protected $usage = '/corona';

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

        $dataFull = file_get_contents('https://www.worldometers.info/coronavirus/country/ukraine/');
        $text = 'Ошибка в получении статистики по Украине.' . PHP_EOL;
        if (preg_match('#' .
            preg_quote('<div style="font-size:13px; color:#999; text-align:center">', '#') .
            'Last updated: (.*?)<\/div>' .
            '.*' .
            '<span style="color\:\#aaa">(.*?) <\/span>' .
            '.*' .
            '<span>(.*?)<\/span>' .
            '.*' .
            '<span>(.*?)<\/span>' .
            '#is',
            $dataFull, $matches)
        ) {
            $dateTime = new \DateTime($matches[1]);
            $dateTime->setTimezone(new \DateTimeZone('Europe/Kiev'));


            echo $dateTime->format('d.m.Y H:i:s');
            $textAr = [];
            $textAr [] = 'Источник: www.worldometers.info';
            $textAr [] = 'Статистика по Украине на ' . $dateTime->format('d.m.Y H:i:s');
            $textAr [] = 'Заболевших: ' . $matches[2];
            $textAr [] = 'Невыздоровевших: ' . $matches[3];
            $textAr [] = 'Исцелённых: ' . $matches[4];
            $text = implode(PHP_EOL, $textAr) . PHP_EOL;
        }

        $dataFull = file_get_contents('https://en.wikipedia.org/wiki/2020_coronavirus_pandemic_in_Ukraine');
        $textExtra = 'Ошибка в получении статистики по Харькову.' . PHP_EOL;

        if (preg_match('#' .
            'The following information was reported as of (.*?) on (.*?):' .
            '.+' .
            'Kharkiv Oblast</a>\s</td>' .
            '\s*<td align="center">(.*?)\s</td>' .
            '\s<td align="center">(.*?)\s<\/td>' .
            '\s<td align="center">(.*?)\s<\/td>' .
            '#uis',
            $dataFull, $matches)
        ) {
            $dateTime = new \DateTime($matches [2] . ' ' . $matches [1], new \DateTimeZone('Europe/Kiev'));

            $textAr = [];
            $textAr [] = 'Источник: en.wikipedia.org';
            $textAr [] = 'Статистика по Харькову на ' . $dateTime->format('d.m.Y H:i:s');
            $textAr [] = 'Заболевших: ' . $matches[3];
            $textAr [] = 'Невыздоровевших: ' . $matches[4];
            $textAr [] = 'Исцелённых: ' . $matches[5];
            $textExtra = implode(PHP_EOL, $textAr);
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text . $textExtra,
        ];

        return Request::sendMessage($data);
    }
}
