<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Request;
use Models\AdminCommand;

/**
 * Currency command
 *
 * Get currencies
 */
class HuiCommand extends AdminCommand
{
    /**
     * @var string
     */
    protected $name = 'hui';

    /**
     * @var string
     */
    protected $description = 'Hui command';

    /**
     * @var string
     */
    protected $usage = '/hui';

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

        $text = 'Хуй-пизда-Джигурда!!!';

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}