<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;
use Models\UserCommand;

/**
 * User "/help" command
 *
 * Command that lists all available commands and displays them in User and Admin sections.
 */
class HelpCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'help';

    /**
     * @var string
     */
    protected $description = 'Show bot commands help';

    /**
     * @var string
     */
    protected $usage = '/help';

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $data = $this->prepareInput();
        $answerText = 'Введите /start для авторизации.';

        $data ['text'] = $answerText;
        $result = Request::sendMessage($data);

        return $result;
    }
}
