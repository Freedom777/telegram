<?php

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Commands\AdminCommand;
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

    /**
     * @var string
     */
    protected $version = '1.0.0';

    const REMIND_NO_ORDER = 'remind_no_order';
    const BILL_SENT = 'bill_sent';
    const SURVEY_FEEDBACK = 'survey_feedback';
    const SURVEY_NOT_BOUGHT = 'survey_not_bought';

    const STATUSES = [
        29361424    => 'Неразобранное',
        29361427    => 'Новое обращение',
        29361430    => 'Заказ Согласован',
        29361433    => 'Договор/счет отправлен',
        29399374    => 'Передан на склад',
        29548384    => 'Заказ Списан',
        31654648    => 'заказ собран с дефицитом',
        30617692    => 'заказ собран без дефицита',
        29362315    => 'Товар отгружен',
        29362318    => 'НЕзавершенные',
        142         => 'Успешно реализовано',
        143         => 'Закрыто и не реализовано',
    ];

    const TIMEZONE = 'Europe/Kiev';

    const STATUS_TO_SEND = 0;
    const STATUS_SENT = 1;

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