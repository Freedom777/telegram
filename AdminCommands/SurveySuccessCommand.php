<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Models\AdminCommand;

/**
 * Admin "/surveysuccess" command
 *
 * Command that demonstrated the Conversation functionality in form of a simple survey.
 */
class SurveySuccessCommand extends AdminCommand
{
    /**
     * @var string
     */
    protected $name = 'surveysuccess';

    /**
     * @var string
     */
    protected $description = 'Survey for success order.';

    /**
     * @var string
     */
    protected $usage = '/surveysuccess';

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
        $answers = ['1', '2', '3', '4', '5'];
        $question = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'surveysuccess.php';
        $result = Request::emptyResponse();

        $data = $this->prepareInput();
        $state = $this->getState();

        switch ($state) {
            case 0:
                if ($this->text === '' || !in_array($this->text, $answers, true)) {
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard($answers))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data ['text'] = $question;
                    $result = Request::sendMessage($data);
                }

                $this->notes ['rate'] = $this->text;
                $this->text = '';

            case 1:
                if (isset($this->notes ['rate']) && in_array($this->notes ['rate'], $answers)) {
                    $this->notes ['state'] = 1;
                    $this->conversation->update();

                    $data = array_merge($data, [
                        'reply_markup' => Keyboard::remove(['selective' => true]),
                        'text' => 'Спасибо за обратную связь, Вы выбрали ' . $this->notes ['rate'],
                    ]);

                    $this->conversation->stop();
                    $result = Request::sendMessage($data);
                }
            break;
        }

        return $result;
    }
}
