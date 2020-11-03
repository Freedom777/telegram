<?php

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use DrillCoder\AmoCRM_Wrap\Contact;
use DrillCoder\AmoCRM_Wrap\Lead;
use Longman\TelegramBot\Request;
use Models\Unsorted;
use Models\UserCommand;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$phone = '+380677749091';
$text = 'Try ... ';

try {
    $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));
    $contact = new Contact();
    $contact->setName('Олег')
        ->addPhone($phone); //Создаём контакт, который будет создан в црм после принятия заявки в неразобранном
    $lead = new Lead();
    $lead->setName('Заказ звонка')
        ->setSale(0); //Создаём сделку, которая будет создана в црм после принятия заявки в неразобранном
    $unsorted = new Unsorted('Форма обратного звонка', $lead, [$contact], UserCommand::PIPELINE_ID);
    $unsorted->addNote('Позвонить по номеру ' . $phone)
        ->save(); // Сохраняем всё в неразобранное в црм

    $text .= 'Спасибо, мы свяжемся с Вами в ближайшее время.';
    // $result = Request::sendMessage($data);

} catch (AmoWrapException $e) {
    $text .= $e->getMessage();
}

// var_dump(AmoCRM::getCustomFields('lead'));
die($text);

$pipelineId = 1979362; // id Воронки
$statusId = 142; // id Статуса: Успешно реализовано
/** @var \DrillCoder\AmoCRM_Wrap\Lead[] $leads */
$leads = $amo->searchLeads(null, $pipelineId, [$statusId], 0, 0, [], new DateTime(date('Y-m-d 00:00:00')));

/** @inherited $lead */
$leadsAr = [];
foreach ($leads as $lead) {
    $leadsAr[] = $lead->getId() . ', ' . $lead->g . ': ' . implode(',', $lead->getMainContact()->getPhones());
    // $leadsAr[] = $lead->getId() . ', ' . $lead->getName() . ': ' . implode(',', $lead->getMainContact()->getPhones());
}
echo implode('<br />', $leadsAr);
// var_dump($result);
die();


$contacts =  $amo->searchContacts('0963313314'); //Ищем контакт по телефону и почте
if (!empty($contacts)) {
    $contact = current($contacts);
    $leads = $contact->getLeads();
    if (!empty($leads)) {
        foreach ($leads as $lead) {
            echo $lead->getName() . ' : ' . $lead->getStatusName() . '<br />';
        }
    }
    // $userId = $contacts[0]->getId();
}

die();

$leads = $amo->searchLeads(null,null,[],0,0,(int)$userId);
var_dump($leads);
die();

die();


var_dump(AmoCRM::getUsers());
die();

/*// Получаем список воронок
$pipelines = AmoCRM::getPipelinesName();

// Берем id первой воронки
$pipelineId = key($pipelines);

// Получаем список статусов
$statuses = AmoCRM::getStatusesName($pipelineId);

var_dump($pipelineId, $statuses);
die();*/
