<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use DrillCoder\AmoCRM_Wrap\Contact;
use Models\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/status" command
 *
 * Get status info for any place.
 * This command requires an API key to be set via command config.
 */
class StatusCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'status';

    /**
     * @var string
     */
    protected $description = 'Show status by phone';

    /**
     * @var string
     */
    protected $usage = '/status';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * @const array
     */
    const STATUSES_NOT_SHOW = [142, 143];

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $data = $this->prepareInput();
        $answerText = 'Введите /start для авторизации.';

        $amocrmUserId = $this->getContactIdByChatId($this->user_id);

        if (!empty($amocrmUserId)) {
            try {
                $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));
            } catch (AmoWrapException $e) {
                $answerText = self::ERROR_AMOCRM;
            }

            $contact = new Contact($amocrmUserId);
            if (!empty($contact)) {
                $leads = $contact->getLeads();

                $answerText = '';
                if (!empty($leads)) {
                    foreach ($leads as $lead) {
                        if (!in_array($lead->getStatusId(), self::STATUSES_NOT_SHOW)) {
                            $answerText .= $lead->getName() . ' : ' . $lead->getStatusName() . PHP_EOL;
                        }
                    }
                }
                if ('' === $answerText) {
                    $answerText .= self::LEADS_NOT_FOUND;
                }
            }
        }

        $data ['text'] = $answerText;
        $result = Request::sendMessage($data);

        return $result;
    }
}