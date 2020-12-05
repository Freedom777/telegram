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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;

/**
 * User "/date" command
 *
 * Shows the date and time of the location passed as the parameter.
 *
 * A Google API key is required for this command, and it can be set in your hook file:
 * $telegram->setCommandConfig('date', ['google_api_key' => 'your_api_key']);
 */
class DateCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'date';

    /**
     * @var string
     */
    protected $description = 'Show date/time by location';

    /**
     * @var string
     */
    protected $usage = '/date <location>';

    /**
     * Guzzle Client object
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Base URI for Google Maps API
     *
     * @var string
     */
    private $google_api_base_uri = 'https://maps.googleapis.com/maps/api/';

    /**
     * Base URI for Google Maps API
     *
     * @var string
     */
    private $collect_api_base_uri = 'https://api.collectapi.com/';

    /**
     * The Google API Key from the command config
     *
     * @var string
     */
    private $google_api_key;

    /**
     * Date format
     *
     * @var string
     */
    private $date_format = 'd-m-Y H:i:s';

    /**
     * Get coordinates of passed location
     *
     * @param string $location
     *
     * @return array
     */
    private function getCoordinates($location)
    {
        $path  = 'geocode/json';
        $query = ['address' => urlencode($location)];

        if ($this->google_api_key !== null) {
            $query['key'] = $this->google_api_key;
        }

        try {
            $response = $this->client->get($path, ['query' => $query]);
        } catch (RequestException $e) {
            TelegramLog::error($e->getMessage());

            return [];
        }

        if (!($data = $this->validateResponseData($response->getBody()))) {
            return [];
        }

        $result = $data['results'][0];
        $lat    = $result['geometry']['location']['lat'];
        $lng    = $result['geometry']['location']['lng'];
        $acc    = $result['geometry']['location_type'];
        $types  = $result['types'];

        return [$lat, $lng, $acc, $types];
    }

    /**
     * Get date for location passed via coordinates
     *
     * @param string $lat
     * @param string $lng
     *
     * @return array
     */
    private function getDate($lat, $lng)
    {
        $path = 'timezone/json';

        $date_utc  = new \DateTimeImmutable(null, new \DateTimeZone('UTC'));
        $timestamp = $date_utc->format('U');

        $query = [
            'location'  => urlencode($lat) . ',' . urlencode($lng),
            'timestamp' => urlencode($timestamp),
        ];

        if ($this->google_api_key !== null) {
            $query['key'] = $this->google_api_key;
        }

        try {
            $response = $this->client->get($path, ['query' => $query]);
        } catch (RequestException $e) {
            TelegramLog::error($e->getMessage());

            return [];
        }

        if (!($data = $this->validateResponseData($response->getBody()))) {
            return [];
        }

        $local_time = $timestamp + $data['rawOffset'] + $data['dstOffset'];

        return [$local_time, $data['timeZoneId']];
    }

    /**
     * Evaluate the response data and see if the request was successful
     *
     * @param string $data
     *
     * @return array
     */
    private function validateResponseData($data)
    {
        if (empty($data)) {
            return [];
        }

        $data = json_decode($data, true);
        if (empty($data)) {
            return [];
        }

        /*if (isset($data['status']) && $data['status'] !== 'OK') {
            return [];
        }*/
        if (empty($data['Results'])) {
            return [];
        }

        return $data['Results'];
    }

    /**
     * Get formatted date at the passed location
     *
     * @param string $location
     *
     * @return string
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function getFormattedDate($location)
    {
        if ($location === null || $location === '') {
            return 'The time in nowhere is never';
        }

        list($lat, $lng) = $this->getCoordinates($location);
        if (empty($lat) || empty($lng)) {
            return 'It seems that in "' . $location . '" they do not have a concept of time.';
        }

        list($local_time, $timezone_id) = $this->getDate($lat, $lng);

        $date_utc = new \DateTimeImmutable(gmdate('Y-m-d H:i:s', $local_time), new \DateTimeZone($timezone_id));

        return 'The local time in ' . $timezone_id . ' is: ' . $date_utc->format($this->date_format);
    }

    public function execute()
    {
        $message = $this->getMessage();

        $chat_id  = $message->getChat()->getId();
        $text = 'You must specify location in format: /date <city>';

        $location = $message->getText(true);

        if (!empty($location)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://devru-latitude-longitude-find-v1.p.rapidapi.com/latlon.php?location=" . urlencode($location),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-rapidapi-host: devru-latitude-longitude-find-v1.p.rapidapi.com",
                    "x-rapidapi-key: 8b0605dc36mshd58afde35b7aa9cp15d05cjsne7966de4026f"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (!empty($err)) {
                $text = 'Ошибка при получении данных.';
                TelegramLog::error($err);
            } else {
                if (!($data = $this->validateResponseData($response))) {
                    $text = 'Город не найден.';
                } else {
                    $json = file_get_contents('http://worldtimeapi.org/api/timezone/'.$data[0]['tz'].'.json');
                    $responseData = json_decode($json, true);
                    list($date, $time) = explode('T', explode('.', $responseData ['datetime'])[0]);

                    $text = 'Таймзона: ' . $data[0]['tz'] . ', дата: ' . $date . ', текущее время ' . $time;
                }
            }
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeOld()
    {
        $message = $this->getMessage();

        $chat_id  = $message->getChat()->getId();
        $location = $message->getText(true);
        $text = 'You must specify location in format: /date <city>';
        $key = trim($this->getConfig('collect_api_key'));

        if ($location !== '') {
            $opts = ['http' =>
                [
                    'method'  => 'GET',
                    'header'  => 'Content-Type: application/json' . "\r\n".
                        'authorization: ' . $key . "\r\n",
                    /*'content' => $body,*/
                    'timeout' => 60
                ]
            ];
            $context  = stream_context_create($opts);
            $path = 'time/timeZone?data.city='.urlencode($location);
            // $result = file_get_contents($this->collect_api_base_uri . $path, false, $context);
            // var_dump($result);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch,CURLOPT_URL,$this->collect_api_base_uri . $path);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
            curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json', 'authorization: ' . $key]);
            $result = curl_exec($ch);
            curl_close($ch);
var_dump($result);
            /*


            $query = [
                'data.city'  => urlencode($location),
            ];
            $path = 'time/timeZone?data.city='.urlencode($location);
            $key = trim($this->getConfig('collect_api_key'));
            $this->client = new Client(['base_uri' => $this->collect_api_base_uri]);
            $headers = [
                'authorization' => $key,
                'content-type' => 'application/json',
            ];

            try {
                $response = $this->client->get($path, ['query' => $query, 'headers' => $headers]);
            } catch (RequestException $e) {
                echo $e->getMessage();
                TelegramLog::error($e->getMessage());
            }*/
// var_dump($response);
            // if (!($data = $this->validateResponseData($response->getBody()))) {
            if (!($data = $this->validateResponseData($result))) {
                $text = 'Город не найден';
            } else {
                $text = $data ['result'] ['name'] . ', текущее время ' . $data ['result'] ['saat'];
            }
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);


        /*curl --request GET \
    --url 'https://api.collectapi.com/time/timeZone?data.city=paris' \
    --header 'authorization: apikey 7pHsRlW2LtEHci7x5qGml5:5MMNG9KfRhmSbvoGgUGqS8' \
    --header 'content-type: application/json'*/

        //First we set up the necessary member variables.
        /*$this->client = new Client(['base_uri' => $this->google_api_base_uri]);
        if (($this->google_api_key = trim($this->getConfig('google_api_key'))) === '') {
            $this->google_api_key = null;
        }

        $message = $this->getMessage();

        $chat_id  = $message->getChat()->getId();
        $location = $message->getText(true);

        $text = 'You must specify location in format: /date <city>';

        if ($location !== '') {
            $text = $this->getFormattedDate($location);
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);*/
    }
}
