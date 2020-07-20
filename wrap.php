<?php

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));
} catch (AmoWrapException $e) {
    die($e->getMessage());
}

var_dump(AmoCRM::getCustomFields('lead'));
die();

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
