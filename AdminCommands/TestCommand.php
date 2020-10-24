<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Models\AdminCommand;
use Longman\TelegramBot\Request;

class TestCommand extends AdminCommand {
    /**
     * @var string
     */
    protected $name = 'test';

    /**
     * @var string
     */
    protected $description = 'Test Command.';

    /**
     * @var string
     */
    protected $usage = '/test';

    public function execute()
    {
        $chat_id = 688516706;

        //Preparing Response
        $msg = $this->getMessage();
        $text    = trim($msg->getText(true));
        $data = [
            'chat_id' => $chat_id,
        ];
        $question = require TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'surveysuccess.php';
        $answers = ['1', '2', '3', '4', '5'];

        //Conversation start
        $this->conversation = new Conversation($chat_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        //cache data from the tracking session if any
        $state = 0;
        if (isset($notes['state'])) {
            $state = $notes['state'];
        }

        $result = Request::emptyResponse();

        //State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        switch ($state) {
            case 0:
                if ($text === '' || !in_array($text, $answers, true)) {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard($answers))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data ['text'] = $question;
                    if ($text !== '') {
                        $data ['text'] = $question;
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes ['surveysuccess'] = $text;
                $text = '';

            case 1:
                if ($text === '') {
                    $notes ['state'] = 1;
                    $this->conversation->update();
                    unset($notes ['state']);

                    $data = array_merge($data, [
                        'reply_markup' => Keyboard::remove(['selective' => true]),
                        'text' => 'Спасибо за обратную связь, Вы выбрали ' . $notes ['surveysuccess'],
                    ]);

                    $this->conversation->stop();
                    $result = Request::sendMessage($data);
                }
                break;
        }

        return $result;
    }
}