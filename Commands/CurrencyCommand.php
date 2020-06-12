<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Currency command
 *
 * Get currencies
 */
class CurrencyCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'currency';

    /**
     * @var string
     */
    protected $description = 'Currency command';

    /**
     * @var string
     */
    protected $usage = '/currency';

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

        $dataFull = file_get_contents('https://obmenka.kharkov.ua/');
        $text = 'Не могу получить курсы';

        if (preg_match('#WUJS\.Load\(\'app-container\', (.*?), formComponent\);#', $dataFull, $matches)) {
            $dataStripped = json_decode($matches[1], true);
            $datetime = $dataStripped ['datetime'];
            $infoAr = [];
            foreach ($dataStripped ['rates'] as $rate) {
                $infoAr [] = $rate ['currencyBase'] . '-' . $rate ['currencyQuoted'] . ' : ' . $rate ['rateBid'] . ' / ' . $rate ['rateAsk'];
            }
            $text = 'Дата: ' . $datetime . PHP_EOL . implode(PHP_EOL, $infoAr);
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}