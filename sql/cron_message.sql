--
-- Структура таблицы `cron_message`
--

CREATE TABLE `cron_message` (
  `id` int(11) NOT NULL,
  `amocrm_user_id` int(10) UNSIGNED NOT NULL,
  `amocrm_lead_id` int(10) UNSIGNED DEFAULT NULL,
  `amocrm_status_id` int(10) UNSIGNED DEFAULT NULL,
  `chat_id` int(11) DEFAULT NULL,
  `phones` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `type` enum('remind_no_order','bill_sent','survey_feedback','survey_not_bought') COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;