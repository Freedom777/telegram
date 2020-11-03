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
use Models\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Request;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StartCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $data = $this->prepareInput();
        $result = Request::emptyResponse();
        $state = $this->getState();

        switch ($state) {
            // Приветствие, запрос номера телефона
            case 0:
                if ($this->text === '') {
                    $this->notes ['state'] = 0;
                    $this->conversation->update();

                    $data ['text'] = 'Здравствуйте, я чат-бот Ликант - инструмент.'; // Я умею отдавать статус заказа по номеру телефона. Нажмите "Статус заказа" для продолжения.';
                    $data ['text'] .= PHP_EOL . 'Введите номер телефона (10 цифр, пример 050*******) для авторизации:';
                    $data ['reply_markup'] = Keyboard::remove(['selective' => true]);

                    $result = Request::sendMessage($data);
                    break;
                }

                $this->notes ['phone'] = $this->text;
                $this->text = '';

            case 1:
                // Проверка телефона на существование в базе, получение списка сделок
                $phone = $this->notes ['phone'];
                if ($phone !== '' && is_numeric($phone) && $phone > 0 && 10 == strlen($phone)) {
                    $this->notes ['state'] = 1;
                    $this->conversation->update();
                    $contactId = null;

                    $amocrmUserId = $this->getAmocrmUserIdByPhone($phone);
                    if (!empty($amocrmUserId)) {
                        $this->renderMenu($data);
                        $this->conversation->stop();
                    } else {
                        try {
                            $amo = new AmoCRM(getenv('AMOCRM_DOMAIN'), getenv('AMOCRM_USER_EMAIL'), getenv('AMOCRM_USER_HASH'));

                            $contacts = $amo->searchContacts($phone); // Ищем контакт по телефону и почте
                            if (!empty($contacts)) {
                                $contact = current($contacts);
                                $amocrmUserId = $contact->getId();
                                $this->renderMenu($data);
                                $this->conversation->stop();
                            } else {
                                $this->renderError($data, $phone);
                            }
                        } catch (AmoWrapException $e) {
                            $data ['text'] = self::ERROR_AMOCRM;
                        }
                    }
                    $this->checkInsertUser($phone, $amocrmUserId);
                } else {
                    $data ['text'] = 'Вы должны указать 10 цифр в качестве номера телефона.';
                    $this->notes ['state'] = 1;
                }
                $result = Request::sendMessage($data);

            case 2:
                // Запуск команды из меню
                if ($this->text != '') {
                    $choice = $this->text;

                    $this->notes ['state'] = 2;
                    $this->conversation->update();

                    switch ($choice) {
                        case self::MENU_ORDER_STATUS:
                            // $this->notes ['choice'] = '/status';
                            $result = $this->getTelegram()->executeCommand('status');
                            // $result = (new StatusCommand($this->getTelegram()))->execute();
                            break;
                    }

                    $this->conversation->stop();
                }
                break;
        }

        return $result;
    }

    protected function renderMenu(&$data)
    {
        // При успешной авторизации вывод меню
        $data ['reply_markup'] = new InlineKeyboard([]);
        $data ['reply_markup']
            ->addRow(new InlineKeyboardButton(['callback_data' => '/status', 'text' => self::MENU_ORDER_STATUS]))
            ->addRow(new InlineKeyboardButton(['callback_data' => '/history', 'text' => self::MENU_HISTORY]))
            ->addRow(new InlineKeyboardButton(['callback_data' => '/catalog', 'text' => self::MENU_CATALOG]))
            ->addRow(new InlineKeyboardButton(['url' => getenv('CHANNEL_INVITE_LINK'), 'text' => self::MENU_NEWS_CHANNEL]))
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);

        $data ['text'] = self::SUCCESS_LOGIN;

        return $data;
    }

    protected function renderError(&$data, $phone)
    {
        $data ['reply_markup'] = new InlineKeyboard([]);
        $data ['reply_markup']
            ->addRow(new InlineKeyboardButton(['callback_data' => '/callrequire' . ' ' . $phone, 'text' => self::MENU_REQUIRE_CALL]))
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);
        $data ['text'] = vsprintf(self::ERROR_PHONE_NOT_FOUND, [getenv('AMOCRM_MANAGER_PHONE_1'), getenv('AMOCRM_MANAGER_PHONE_2')]);

        return $data;

    }
}
/*// Получаем список воронок
                            $pipelines = AmoCRM::getPipelinesName();

                            // Берем id первой воронки
                            $pipelineId = key($pipelines);

                            // Получаем список статусов
                            $statuses = AmoCRM::getStatusesName($pipelineId);*/