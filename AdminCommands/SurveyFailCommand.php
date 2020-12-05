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

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Models\AdminCommand;
use Models\Logic;

/**
 * Admin "/surveyfail" command
 *
 * Command that demonstrated the Conversation funtionality in form of a simple survey.
 */
class SurveyFailCommand extends AdminCommand
{
    /**
     * @var string
     */
    protected $name = 'surveyfail';

    /**
     * @var string
     */
    protected $description = 'Survey for failed order.';

    /**
     * @var string
     */
    protected $usage = '/surveyfail';

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
        $state = $this->getConversationState();

        $result = Request::emptyResponse();

        switch ($state) {
            case 0:
                if ($this->text === '') {
                    $this->conversation->update();
                    $data ['text'] = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'surveyfail.php';

                    Request::sendMessage($data);
                }

                $this->notes ['reason'] = $this->text;
                $this->text = '';

            case 1:
                if (isset($this->notes ['reason']) && strlen($this->notes ['reason']) > 0) {
                    $this->notes ['state'] = 1;
                    $this->conversation->update();

                    $data = [
                        'reply_markup' => Keyboard::remove(['selective' => true]),
                        'text' => 'Спасибо за обратную связь, Вы выбрали ' . $this->notes ['rate'],
                    ];
                    $result = Request::sendMessage($data);

                    $sender = Logic::getAmocrmUsers([
                        'fields' => 'phone',
                        'filters' => [
                            'chat_id' => $this->chat_id,
                            'amocrm_user_type' => self::$AMOCRRM_USER_TYPE_USER,
                        ],
                        'limit' => 1
                    ]);
                    if (!empty($sender) && !empty($sender[0]) && !empty($sender[0]['phone'])) {
                        $phone = $sender[0]['phone'];

                        $receivers = Logic::getAmocrmUsers([
                            'fields' => 'chat_id',
                            'filters' => ['amocrm_user_type' => [self::$AMOCRRM_USER_TYPE_ADMIN, self::$AMOCRRM_USER_TYPE_MANAGER]]
                        ]);

                        $data = [
                            'text' => 'Пользователь с номером телефона ' . $phone .
                                ' и невыполненным заказом оценил работу магазина ' . $this->notes ['rate'] . ' / 5',
                        ];
                        foreach ($receivers as $receiver) {
                            $chatData = array_merge($data, ['chat_id' => $receiver ['chat_id']]);
                            Request::sendMessage($chatData);
                        }
                    }
                    $this->conversation->stop();
                }
                break;
        }

        return $result;
    }
}
