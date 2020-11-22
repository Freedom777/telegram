<?php

namespace Models;

use Longman\TelegramBot\Commands\AdminCommand as AdminCommandBase;

abstract class AdminCommand extends AdminCommandBase {
    use CommandTrait;

    /**
     * @var bool
     */
    protected $private_only = true;

    const REMIND_NO_ORDER = 'remind_no_order';
    const BILL_SENT = 'bill_sent';
    const SURVEY_FEEDBACK = 'survey_feedback';
    const SURVEY_NOT_BOUGHT = 'survey_not_bought';

}