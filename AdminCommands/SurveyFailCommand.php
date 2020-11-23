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

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Request;
use Models\AdminCommand;

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
     * @var string
     */
    protected $version = '0.3.0';

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
                        'text' => 'Спасибо за обратную связь!',
                    ];
                    $result = Request::sendMessage($data);
                }
                break;
        }

        return $result;
    }
}
