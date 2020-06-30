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

