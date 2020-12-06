<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\TelegramLog;
use Models\BasePdo;
use Models\Logic;
use Models\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/jaestgrut" command
 *
 * Get Admin role.
 */
class JaestgrutCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'jaestgrut';

    /**
     * @var string
     */
    protected $description = 'Get Admin role';

    /**
     * @var string
     */
    protected $usage = '/jaestgrut';

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
            'filters' => ['chat_id' => $this->user_id],
            'limit' => 1
        ]);
        TelegramLog::error(var_export($amocrmUsers, true));
        if (!empty($amocrmUsers)) {
            $id = $amocrmUsers [0] ['id'];

            $result = Logic::updateAmocrmUser([
                'amocrm_user_type' => self::$AMOCRRM_USER_TYPE_ADMIN,
                'updated_at' => BasePdo::now(),
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