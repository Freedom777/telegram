<?php


namespace Longman\TelegramBot\Commands\UserCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use DrillCoder\AmoCRM_Wrap\Contact;
use DrillCoder\AmoCRM_Wrap\Lead;
use DrillCoder\AmoCRM_Wrap\Unsorted;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Exception\TelegramException;
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
        $fio = $this->user->getFirstName() . $this->user->getLastName();
        $input = json_decode($this->getTelegram()->getCustomInput());

        $phone = $input->phone;
        $result = Request::emptyResponse();

        try {
            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));
            $contact = new Contact();
            $contact->setName($fio)
                ->addPhone($phone); //Создаём контакт, который будет создан в црм после принятия заявки в неразобранном
            $lead = new Lead();
            $lead->setName('Заказ звонка')
                ->setSale(0); //Создаём сделку, которая будет создана в црм после принятия заявки в неразобранном
            $unsorted = new Unsorted('Форма обратного звонка', $lead, [$contact], self::PIPELINE_NAME);
            $unsorted->addNote('Позвонить по номеру +38' . $phone)
                ->save(); // Сохраняем всё в неразобранное в црм

            $data['text'] = 'Спасибо, мы свяжемся с Вами в ближайшее время.';
            $result = Request::sendMessage($data);
        } catch (AmoWrapException $e) {
            TelegramLog::error($e); // die($e->getMessage()); //Прерывем работу скрипта и выводим текст ошибки
        }

        return $result;
    }
}