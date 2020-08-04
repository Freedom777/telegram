<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Models\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;

/**
 * User "/status" command
 *
 * Get status info for any place.
 * This command requires an API key to be set via command config.
 */
class StatusCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'status';

    /**
     * @var string
     */
    protected $description = 'Show status by phone';

    /**
     * @var string
     */
    protected $usage = '/status';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * @var int
     */
    protected $chat_id;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $text;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $this->prepareInput();

        //Preparing Response
        $data = [
            'chat_id' => $this->chat_id,
        ];
        $answerText = '';

        if (!empty($_SESSION ['user'])) {
            $leads = $_SESSION ['user'] ['leads'];
            foreach ($leads as $leadName => $leadStatusName) {
                $answerText .= $leadName . ' : ' . $leadStatusName . PHP_EOL;
            }
        } else {
            $answerText = 'Введите /start для авторизации.';
        }

        $data ['text'] = $answerText;
        $result = Request::sendMessage($data);

        return $result;
    }
}