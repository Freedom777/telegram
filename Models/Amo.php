<?php

namespace Models;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use DrillCoder\AmoCRM_Wrap\Contact;
use DrillCoder\AmoCRM_Wrap\Lead;
use Longman\TelegramBot\TelegramLog;

class Amo {
    /**
     * @param array $options
     *
     * @throws \DrillCoder\AmoCRM_Wrap\AmoWrapException
     */
    public static function createUnsorted(array $options = []) {
        try {
            $default = [
                'fio' => 'ФИО',
                'phone' => 'Номер телефона',
                'leadName' => 'Сделка',
                'formName' => 'Имя формы',
                'note' => 'Примечание',
            ];
            $options = array_merge($default, $options);

            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));
            $contact = new Contact();
            $contact->setName($options ['fio'])
                ->addPhone($options ['phone']); //Создаём контакт, который будет создан в црм после принятия заявки в неразобранном
            $lead = new Lead();
            $lead->setName($options ['leadName']);
            $unsorted = new Unsorted($options ['formName'], $lead, [$contact], CommandTrait::$PIPELINE_ID);
            $unsorted->addNote($options ['note'])
                ->save(); // Сохраняем всё в неразобранное в црм
        } catch (AmoWrapException $e) {
            TelegramLog::error($e);
        }
    }
}