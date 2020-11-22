<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Models\AdminCommand;
use Models\Queries;

/**
 * Admin "/remindorder" command
 *
 * Command that demonstrated the Conversation functionality in form of a simple survey.
 */
class RemindOrderCommand extends AdminCommand
{
    /**
     * @var string
     */
    protected $name = 'remindorder';

    /**
     * @var string
     */
    protected $description = 'Remind order after AMOCRM_SUCCESS_ORDER_REMINDER_DAYS and if no Order - AMOCRM_SUCCESS_ORDER_AGAIN_REMIND_DAYS.';

    /**
     * @var string
     */
    protected $usage = '/remindorder';

    /**
     * @var string
     */
    protected $version = '0.3.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $result = Request::emptyResponse();

        $data = $this->prepareInput();
        $state = $this->getState();

        switch ($state) {
            case 0:
                if ($this->text === '') {
                    $this->conversation->update();

                    $phone = Queries::getAmocrmUserByChatId($this->chat_id);

                    $data ['reply_markup'] = new InlineKeyboard([]);
                    $data ['reply_markup']
                        ->addRow(new InlineKeyboardButton(['callback_data' => '/callrequire' . ' ' . $phone, 'text' => self::MENU_REQUIRE_CALL]))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data ['text'] = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'remind.php';
                    $result = Request::sendMessage($data);
                }

                $this->notes ['reason'] = $this->text;
                $this->text = '';

            case 1:
                if (isset($this->notes ['reason']) && strlen($this->notes ['reason']) > 0) {
                    $this->notes ['state'] = 1;
                    $this->conversation->update();

                    $data = array_merge($data, [
                        'reply_markup' => Keyboard::remove(['selective' => true]),
                        'text' => 'Спасибо за обратную связь!',
                    ]);

                    $this->conversation->stop();
                    $result = Request::sendMessage($data);
                }
                break;
        }

        return $result;
    }
}
