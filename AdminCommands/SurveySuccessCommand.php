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
    protected $description = 'Survey for success buy.';

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

                $notes ['rate'] = $this->text;
                $this->text = '';

            case 1:
                if (in_array($this->notes ['rate'], $answers)) {
                    $notes ['state'] = 1;
                    $this->conversation->update();

                    $data = array_merge($data, [
                        'reply_markup' => Keyboard::remove(['selective' => true]),
                        'text' => 'Спасибо за обратную связь, Вы выбрали ' . $notes ['rate'],
                    ]);
                    $result = Request::sendMessage($data);

                    // unset($notes ['state']);
                    $this->conversation->stop();
                }
            break;
        }

        return $result;
    }
}
/* else {
                    $data = array_merge($data, [
                        'text' => $question,
                    ]);
                    $this->notes ['state'] = 1;
                    $result = Request::sendMessage($data);
                }*/