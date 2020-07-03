<?php


namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;

class CatalogCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'catalog';

    /**
     * @var string
     */
    protected $description = 'Show Catalog';

    /**
     * @var string
     */
    protected $usage = '/catalog';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $private_only = false;

    /**
     * Show Catalog
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute() {
        try {
            $message = $this->getMessage();
            $chat    = $message->getChat();
            $chat_id = $chat->getId();

            $data = [
                'chat_id' => $chat_id,
                'text' => 'Каталог TOOLS.UA',
            ];
            $data ['reply_markup'] = new InlineKeyboard([]);
            $data ['reply_markup']->addRow(new InlineKeyboardButton(['url' => 'https://toolsua.com/katalog/abrazivnyij/', 'text' => 'Абразивный инструмент']));
            $data ['reply_markup']->addRow(new InlineKeyboardButton(['url' => 'https://toolsua.com/katalog/meritelnyij-instrument/', 'text' => 'Мерительный инструмент']));
            $data ['reply_markup']->addRow(new InlineKeyboardButton(['url' => 'https://toolsua.com/katalog/metallorezhushhij/', 'text' => 'Металлорежущий инструмент']));
            $data ['reply_markup']->addRow(new InlineKeyboardButton(['url' => 'https://toolsua.com/katalog/osnastka/', 'text' => 'Оснастка']));
            $data ['reply_markup']->addRow(new InlineKeyboardButton(['url' => 'https://toolsua.com/katalog/slesarnyij/', 'text' => 'Слесарный инструмент']));
            $data ['reply_markup']
                ->setResizeKeyboard(true)
                ->setOneTimeKeyboard(true)
                ->setSelective(true);

            $result = Request::sendMessage($data);
        } catch (TelegramException $e) {
            TelegramLog::error($e);
        }

        return $result;
    }
}