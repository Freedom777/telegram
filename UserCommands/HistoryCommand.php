<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use DrillCoder\AmoCRM_Wrap\Contact;
use Models\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/history" command
 *
 * Get status info for any place.
 * This command requires an API key to be set via command config.
 */
class HistoryCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'history';

    /**
     * @var string
     */
    protected $description = 'Show history by phone';

    /**
     * @var string
     */
    protected $usage = '/history';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * @var int
     */
    protected $chat_id;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $text;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $this->prepareInput();

        //Preparing Response
        $data = [
            'chat_id' => $this->chat_id,
        ];
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
                        // Вывод всех сделок в текущем году
                        if ($lead->getDateCreate()->format('Y') == date('Y')) {
                            $answerText .= $lead->getName() . ' : ' . $lead->getStatusName() . PHP_EOL;
                        }
                    }
                }
                if (empty($answerText)) {
                    $answerText .= self::LEADS_NOT_FOUND;
                }
            }
        }

        $data ['text'] = $answerText;
        $result = Request::sendMessage($data);

        return $result;
    }
}