<?php


/**
 * Created by PhpStorm.
 * User: DrillCoder
 * Date: 09.10.2017
 * Time: 12:51
 */

namespace Models;

use DrillCoder\AmoCRM_Wrap\AmoCRM;
use DrillCoder\AmoCRM_Wrap\AmoWrapException;
use DrillCoder\AmoCRM_Wrap\Base;
use DrillCoder\AmoCRM_Wrap\Company;
use DrillCoder\AmoCRM_Wrap\Contact;
use DrillCoder\AmoCRM_Wrap\Helpers\Config;
use DrillCoder\AmoCRM_Wrap\Lead;
use DrillCoder\AmoCRM_Wrap\Note;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;

/**
 * Class Unsorted
 * @package DrillCoder\AmoCRM_Wrap
 */
class Unsorted extends Base
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $formName;

    /**
     * @var int
     */
    private $pipelineId;

    /**
     * @var Contact[]|array
     */
    private $contacts = array();

    /**
     * @var Lead|array
     */
    private $lead = array();

    /**
     * @var Company[]|array
     */
    private $companies = array();

    /**
     * @var Note[]
     */
    private $notes;

    /**
     * @param string $formName
     * @param Contact[] $contacts
     * @param Lead $lead
     * @param int|string|null $pipeline
     * @param Company[] $companies
     *
     * @throws AmoWrapException
     */
    public function __construct($formName, $lead, $contacts = array(), $pipeline = null, $companies = array())
    {
        if (!AmoCRM::isAuthorization()) {
            throw new AmoWrapException('Требуется авторизация');
        }

        $this->contacts = $contacts;
        $this->lead = $lead;
        $this->companies = $companies;
        $this->formName = $formName;
        if ($pipeline !== null) {
            $this->pipelineId = AmoCRM::searchPipelineId($pipeline);
        }
    }

    /**
     * @return Unsorted
     *
     * @throws AmoWrapException
     */
    public function save()
    {
        if (!empty($this->lead) || !empty($this->contacts)) {
            $lead = null;
            if (!empty($this->lead)) {
                $lead = $this->lead->getRaw();
                if (!empty($this->notes)) {
                    foreach ($this->notes as $note) {
                        $lead['notes'][] = $note->getRaw();
                    }
                }
            }
            $contacts = array();
            foreach ($this->contacts as $contact) {
                $contacts[] = $contact->getRaw();
            }
            $companies = array();
            foreach ($this->companies as $company) {
                $companies[] = $company->getRaw();
            }
            $request['add'] = array(
                array(
                    'source_name' => 'DrillCoder AmoCRM Wrap',
                    'created_at' => date('U'),
                    'pipeline_id' => $this->pipelineId,
                    'incoming_entities' => array(
                        'leads' => array($lead),
                        'contacts' => $contacts,
                        'companies' => $companies,
                    ),
                    'incoming_lead_info' => array(
                        'form_id' => 25,
                        'form_page' => $this->formName,
                    ),
                ),
            );
            TelegramLog::notice(var_export($request));
            $response = self::cUrl('api/v2/incoming_leads/form', $request);
            if ($response !== null && $response->status === 'success') {
                $this->id = $response->data[0];
                return $this;
            }
        }
        throw new AmoWrapException('Не удалось сохранить заявку в неразобранное');
    }

    /**
     * @param string $text
     * @param int $type
     *
     * @return Unsorted
     *
     * @throws AmoWrapException
     */
    public function addNote($text, $type = 4)
    {
        $note = new Note();
        $note->setText($text)
            ->setType($type)
            ->setElementType('lead');
        $this->notes[] = $note;
        return $this;
    }

    /**
     * @param string        $url
     * @param array         $data
     * @param \DateTime|null $modifiedSince
     * @param bool          $ajax
     *
     * @return \stdClass|null
     *
     * @throws AmoWrapException
     */
    protected static function cUrl($url, $data = array(), \DateTime $modifiedSince = null, $ajax = false)
    {
        $url = 'https://' . self::$domain . '.amocrm.ru/' . $url;
        $isUnsorted = mb_stripos($url, 'incoming_leads') !== false;
        if ($isUnsorted) {
            $url .= '&login=' . self::$userLogin . '&api_key=' . self::$userAPIKey;
        } else {
            if (mb_strpos($url, '?') === false) {
                $url .= '?';
            }
            $url .= '&USER_LOGIN=' . self::$userLogin . '&USER_HASH=' . self::$userAPIKey;
        }

        $curl = curl_init();

        curl_setopt($curl,CURLOPT_COOKIEFILE,LOGS_PATH.'/cookie.txt');
        curl_setopt($curl,CURLOPT_COOKIEJAR,LOGS_PATH.'/cookie.txt');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'DrillCoder AmoCRM_Wrap/v' . AmoCRM::VERSION);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $headers = array();
        if (count($data) > 0) {
            curl_setopt($curl, CURLOPT_POST, true);
            if ($ajax) {
                $headers[] = 'X-Requested-With: XMLHttpRequest';
                $dataStr = $data;
            } elseif ($isUnsorted) {
                $dataStr = http_build_query($data);
            } else {
                $headers[] = 'Content-Type: application/json';
                $dataStr = json_encode($data);
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $dataStr);
        }
        if ($modifiedSince !== null) {
            $headers[] = 'IF-MODIFIED-SINCE: ' . $modifiedSince->format(\DateTime::RFC1123);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $json = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($json);
        if (isset($result->response->error) || (isset($result->title) && $result->title === 'Error')) {
            $errorCode = isset($result->status) ? (int)$result->status : (int)$result->response->error_code;
            $errorMessage = isset(Config::$errors[$errorCode]) ? Config::$errors[$errorCode] : $result->response->error;

            throw new AmoWrapException($errorMessage, $errorCode);
        }

        return $result;
    }
}