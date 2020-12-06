<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Models\Logic;
use Models\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/jaestsuperadmino" command
 *
 * Get Admin role.
 */
class JaestsuperadminoCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'jaestsuperadmino';

    /**
     * @var string
     */
    protected $description = 'Get Admin role';

    /**
     * @var string
     */
    protected $usage = '/jaestsuperadmino';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $data = $this->prepareInput();
        $answerText = 'Настройки не изменены. Неизвестная ошибка.';

        $amocrmUsers = Logic::getAmocrmUsers([
            'fields' => 'id',
            'chat_id' => $this->user_id,
            'limit' => 1
        ]);
        if (!empty($amocrmUsers)) {
            $id = $amocrmUsers [0] ['id'];

            $result = Logic::updateAmocrmUser([
                'amocrm_user_type' => self::$AMOCRRM_USER_TYPE_ADMIN
            ], [
                'id' => $id
            ]);

            if (!empty($result)) {
                $answerText = 'Настройки успешно изменены.';
            }
        }

        $data ['text'] = $answerText;
        $result = Request::sendMessage($data);
    }
}