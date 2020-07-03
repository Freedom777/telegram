<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Request;

class PostNewsCommand extends AdminCommand {
    /**
     * @var string
     */
    protected $name = 'postnews';

    /**
     * @var string
     */
    protected $description = 'Post news to channel';

    /**
     * @var string
     */
    protected $usage = '/postnews <news>';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    public function execute()
    {
        $message = $this->getMessage();
        $text    = trim($message->getText(true));

        $data = [
            'chat_id' => getenv('CHANNEL_CHAT_ID'),
            'text' => $text,
        ];

        Request::sendMessage($data);
    }
}