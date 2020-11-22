<?php


namespace Longman\TelegramBot\Commands\UserCommands;

use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use Models\Amo;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use Models\UserCommand;

class CallRequireCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'callrequire';

    /**
     * @var string
     */
    protected $description = 'Require Call';

    /**
     * @var string
     */
    protected $usage = '/callrequire';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Show Catalog
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute() {
        $data = $this->prepareInput();
        $fio = $this->chat->getFirstName() . (!empty($this->chat->getLastName()) ? ' ' . $this->chat->getLastName() : '') .
            ' (' . $this->chat->getUsername() . ')';
        $phone = '+38' . $this->text;

        try {
            Amo::createUnsorted([
                'fio' => $fio,
                'phone' => $phone,
                'leadName' => 'Заказ звонка',
                'formName' => 'Форма обратного звонка',
                'note' => 'Позвонить по номеру ' . $phone,
            ]);
        } catch (AmoWrapException $e) {
            TelegramLog::error($e);
        }

        $data['text'] = 'Спасибо, мы свяжемся с Вами в ближайшее время.';
        $result = Request::sendMessage($data);

        return $result;
    }
}