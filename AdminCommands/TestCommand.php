<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Models\AdminCommand;
use Longman\TelegramBot\Request;
use Models\Queries;

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
        $result = Queries::getCronMessageIds(
            new \DateTime('2020-07-09 19:00:30'),
            new \DateTime('2020-07-25 19:00:23'),
            self::REMIND_NO_ORDER,
            [self::STATUS_SENT, self::STATUS_REMINDED]
        );
        $data = [
            'chat_id' => $chat_id,
            'text' => var_export($result, true),
        ];
        $result = Request::sendMessage($data);
        die();


        $chat_id = 688516706;
        $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

        //Preparing Response
        $msg = $this->getMessage();
        $text    = trim($msg->getText(true));
        $data = [
            'chat_id' => $chat_id,
            'text' => implode(',', $amo::getPipelinesName()),
        ];



        $result = Request::sendMessage($data);
        die();

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