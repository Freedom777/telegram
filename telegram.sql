-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 29, 2020 at 05:07 PM
-- Server version: 5.7.24
-- PHP Version: 7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `telegram`
--

-- --------------------------------------------------------

--
-- Table structure for table `anecdot`
--

CREATE TABLE `anecdot` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(1022) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `anecdot`
--

INSERT INTO `anecdot` (`id`, `description`) VALUES
(150727, 'Самая тоскливая и непопулярная передача \"Парламентский час\" могла бы\r\nстать самой веселой и самой рейтинговой, если бы ее давали в переводе\r\nГоблина.'),
(256077, 'В суши-баре.\r\n- Будьте добры, роллы с лососем и вместо васаби чилийский хрен...\r\n- Хрен вам, а не васаби... Я правильно записал?'),
(363604, 'Пьяный пожарник упал с 40-метровой пожарной лестницы, но остался цел и\r\nневредим! Его спасло то, что он успел подняться только на вторую\r\nступеньку.'),
(392845, 'Ученые изучили различные факторы потребительского рынка: спрос,\r\nпредложение.\r\nНевероятно, но факт: Перед пасхой куры несут в три раза больше яиц,\r\nчем обычно.'),
(460643, 'Обратите внимание, если женщину случайно застать голой, то она под визг\r\nне закрывает сиськи, а приподнимает их.'),
(465686, 'Не все политики проститутки, есть и сутенёры.'),
(529241, 'Разговаривают две подруги.\r\n- Я встретилась с мужчиной, но он мне не понравился. Как вежливо\r\n  отказать ему в дальнейшем общении?\r\n- Я обычно в таком случае деньги в долг просила - большую сумму, потом\r\n  они сами исчезали...'),
(579565, 'Мозги, конечно, не видно, но когда их не хватает - заметно.'),
(603627, 'Поликлиника. Из кабинета психиатра выходит врач, останавливает пробегающую мимо медсестру и спрашивает:\r\n - Леночка, какое сегодня число?\r\n - 12 апреля, День космонавтики. А в чём дело?\r\n - Да я тут с пациентом беседую, надо проверить, ориентируется ли он во времени.'),
(629427, '- Мне нужно с тобой серьезно поговорить.\r\n- Только если это очень важно.\r\n- Это очень важно. Итак, представь, что ты ромб...'),
(672429, '- Иду я вчера, смотрю, гуляет дамочка с овчаркой без намордника. Я смотрю на овчарку и она на меня. Прошёл мимо, и вдруг сзади толчок в спину, я падаю...\r\n- Не искусала хоть?\r\n- Да нет. Как заорёт: \"Ты чего, козёл, на мою собаку пялишься?!\"'),
(690268, 'Наличие  нескольких любовников у женщины  развивает  логику,  у мужчины – логистику.'),
(843427, 'Когда я пошла работать в психологический центр, то надеялась разобраться с тараканами в голове.\r\nСпустя год работы появилось ощущение, что каждый из них получил неплохое образование и теперь может аргументировано защищаться.'),
(896083, 'У русских есть удивительная черта, которой нет ни у кого в мире - они любят Россию.'),
(896300, 'На концерте \"Нашествие\" избили Шевчука за песню \"Пусть идет дождь...\".');

-- --------------------------------------------------------

--
-- Table structure for table `callback_query`
--

CREATE TABLE `callback_query` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this query',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
  `message_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Unique message identifier',
  `inline_message_id` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Identifier of the message sent via the bot in inline mode, that originated the query',
  `chat_instance` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'Global identifier, uniquely corresponding to the chat to which the message with the callback button was sent',
  `data` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'Data associated with the callback button',
  `game_short_name` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'Short name of a Game to be returned, serves as the unique identifier for the game',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` bigint(20) NOT NULL COMMENT 'Unique identifier for this chat',
  `type` enum('private','group','supergroup','channel') COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Type of chat, can be either private, group, supergroup or channel',
  `title` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT '' COMMENT 'Title, for supergroups, channels and group chats',
  `username` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Username, for private chats, supergroups and channels if available',
  `first_name` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'First name of the other party in a private chat',
  `last_name` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Last name of the other party in a private chat',
  `all_members_are_administrators` tinyint(1) DEFAULT '0' COMMENT 'True if a all members of this group are admins',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update',
  `old_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier, this is filled when a group is converted to a supergroup'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `type`, `title`, `username`, `first_name`, `last_name`, `all_members_are_administrators`, `created_at`, `updated_at`, `old_id`) VALUES
(-418078787, 'group', 'Bot', NULL, NULL, NULL, 1, '2020-04-24 09:15:59', '2020-04-28 06:48:00', NULL),
(688516706, 'private', NULL, 'olegfreedom777', 'Олег', NULL, NULL, '2020-04-23 19:43:14', '2020-04-28 08:06:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chosen_inline_result`
--

CREATE TABLE `chosen_inline_result` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this entry',
  `result_id` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'The unique identifier for the result that was chosen',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'The user that chose the result',
  `location` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Sender location, only for bots that require user location',
  `inline_message_id` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Identifier of the sent inline message',
  `query` text COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'The query that was used to obtain the result',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversation`
--

CREATE TABLE `conversation` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this entry',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique user or chat identifier',
  `status` enum('active','cancelled','stopped') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'active' COMMENT 'Conversation state',
  `command` varchar(160) COLLATE utf8mb4_unicode_520_ci DEFAULT '' COMMENT 'Default command to execute',
  `notes` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Data stored from command',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edited_message`
--

CREATE TABLE `edited_message` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this entry',
  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
  `message_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Unique message identifier',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `edit_date` timestamp NULL DEFAULT NULL COMMENT 'Date the message was edited in timestamp format',
  `text` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For text messages, the actual UTF-8 text of the message max message length 4096 char utf8',
  `entities` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text',
  `caption` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For message with caption, the actual UTF-8 text of the caption'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inline_query`
--

CREATE TABLE `inline_query` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this query',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `location` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Location of the user',
  `query` text COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Text of the query',
  `offset` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Offset of the result',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `chat_id` bigint(20) NOT NULL COMMENT 'Unique chat identifier',
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique message identifier',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `date` timestamp NULL DEFAULT NULL COMMENT 'Date the message was sent in timestamp format',
  `forward_from` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier, sender of the original message',
  `forward_from_chat` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier, chat the original message belongs to',
  `forward_from_message_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier of the original message in the channel',
  `forward_signature` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For messages forwarded from channels, signature of the post author if present',
  `forward_sender_name` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Sender''s name for messages forwarded from users who disallow adding a link to their account in forwarded messages',
  `forward_date` timestamp NULL DEFAULT NULL COMMENT 'date the original message was sent in timestamp format',
  `reply_to_chat` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
  `reply_to_message` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Message that this message is reply to',
  `edit_date` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Date the message was last edited in Unix time',
  `media_group_id` text COLLATE utf8mb4_unicode_520_ci COMMENT 'The unique identifier of a media message group this message belongs to',
  `author_signature` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Signature of the post author for messages in channels',
  `text` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For text messages, the actual UTF-8 text of the message max message length 4096 char utf8mb4',
  `entities` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text',
  `caption_entities` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For messages with a caption, special entities like usernames, URLs, bot commands, etc. that appear in the caption',
  `audio` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Audio object. Message is an audio file, information about the file',
  `document` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Document object. Message is a general file, information about the file',
  `animation` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Message is an animation, information about the animation',
  `game` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Game object. Message is a game, information about the game',
  `photo` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Array of PhotoSize objects. Message is a photo, available sizes of the photo',
  `sticker` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Sticker object. Message is a sticker, information about the sticker',
  `video` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Video object. Message is a video, information about the video',
  `voice` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Voice Object. Message is a Voice, information about the Voice',
  `video_note` text COLLATE utf8mb4_unicode_520_ci COMMENT 'VoiceNote Object. Message is a Video Note, information about the Video Note',
  `caption` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For message with caption, the actual UTF-8 text of the caption',
  `contact` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Contact object. Message is a shared contact, information about the contact',
  `location` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Location object. Message is a shared location, information about the location',
  `venue` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Venue object. Message is a Venue, information about the Venue',
  `poll` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Poll object. Message is a native poll, information about the poll',
  `dice` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Message is a dice with random value from 1 to 6',
  `new_chat_members` text COLLATE utf8mb4_unicode_520_ci COMMENT 'List of unique user identifiers, new member(s) were added to the group, information about them (one of these members may be the bot itself)',
  `left_chat_member` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier, a member was removed from the group, information about them (this member may be the bot itself)',
  `new_chat_title` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'A chat title was changed to this value',
  `new_chat_photo` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Array of PhotoSize objects. A chat photo was change to this value',
  `delete_chat_photo` tinyint(1) DEFAULT '0' COMMENT 'Informs that the chat photo was deleted',
  `group_chat_created` tinyint(1) DEFAULT '0' COMMENT 'Informs that the group has been created',
  `supergroup_chat_created` tinyint(1) DEFAULT '0' COMMENT 'Informs that the supergroup has been created',
  `channel_chat_created` tinyint(1) DEFAULT '0' COMMENT 'Informs that the channel chat has been created',
  `migrate_to_chat_id` bigint(20) DEFAULT NULL COMMENT 'Migrate to chat identifier. The group has been migrated to a supergroup with the specified identifier',
  `migrate_from_chat_id` bigint(20) DEFAULT NULL COMMENT 'Migrate from chat identifier. The supergroup has been migrated from a group with the specified identifier',
  `pinned_message` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Message object. Specified message was pinned',
  `invoice` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Message is an invoice for a payment, information about the invoice',
  `successful_payment` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Message is a service message about a successful payment, information about the payment',
  `connected_website` text COLLATE utf8mb4_unicode_520_ci COMMENT 'The domain name of the website on which the user has logged in.',
  `passport_data` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Telegram Passport data',
  `reply_markup` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Inline keyboard attached to the message'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`chat_id`, `id`, `user_id`, `date`, `forward_from`, `forward_from_chat`, `forward_from_message_id`, `forward_signature`, `forward_sender_name`, `forward_date`, `reply_to_chat`, `reply_to_message`, `edit_date`, `media_group_id`, `author_signature`, `text`, `entities`, `caption_entities`, `audio`, `document`, `animation`, `game`, `photo`, `sticker`, `video`, `voice`, `video_note`, `caption`, `contact`, `location`, `venue`, `poll`, `dice`, `new_chat_members`, `left_chat_member`, `new_chat_title`, `new_chat_photo`, `delete_chat_photo`, `group_chat_created`, `supergroup_chat_created`, `channel_chat_created`, `migrate_to_chat_id`, `migrate_from_chat_id`, `pinned_message`, `invoice`, `successful_payment`, `connected_website`, `passport_data`, `reply_markup`) VALUES
(-418078787, 23, 688516706, '2020-04-24 09:15:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1164525105', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 24, 688516706, '2020-04-24 09:16:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/currency', '[{\"offset\":0,\"length\":9,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 26, 688516706, '2020-04-24 09:16:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Заебись!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 27, 688516706, '2020-04-24 09:16:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Пойду жрать', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 28, 179513218, '2020-04-24 09:16:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Приятного', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 29, 179513218, '2020-04-24 09:17:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/hui', '[{\"offset\":0,\"length\":4,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 31, 688516706, '2020-04-24 09:25:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/hui', '[{\"offset\":0,\"length\":4,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 33, 179513218, '2020-04-24 09:32:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/hui', '[{\"offset\":0,\"length\":4,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 35, 179513218, '2020-04-24 09:32:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/currency', '[{\"offset\":0,\"length\":9,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 37, 688516706, '2020-04-24 09:34:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/weather Харьков', '[{\"offset\":0,\"length\":8,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 39, 688516706, '2020-04-24 09:34:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/weather kharkiv', '[{\"offset\":0,\"length\":8,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 40, 179513218, '2020-04-24 09:34:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Выходи', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 42, 179513218, '2020-04-24 09:35:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Братан', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 43, 688516706, '2020-04-24 09:41:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/weather kharkiv', '[{\"offset\":0,\"length\":8,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 44, 688516706, '2020-04-24 09:43:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/weather Kharkiv', '[{\"offset\":0,\"length\":8,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 46, 688516706, '2020-04-24 11:36:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/weather kharkiv', '[{\"offset\":0,\"length\":8,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 62, 688516706, '2020-04-25 11:13:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Привет!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 64, 688516706, '2020-04-25 11:13:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Как сам?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 66, 688516706, '2020-04-25 11:13:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Что с погодой?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 68, 688516706, '2020-04-25 11:13:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Точно?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 70, 688516706, '2020-04-25 11:14:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Сколько время?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 72, 688516706, '2020-04-25 11:14:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Какие новости?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 73, 688516706, '2020-04-25 11:16:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Какие новости?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 112, 179513218, '2020-04-25 13:59:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Плече потянул (', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 113, 179513218, '2020-04-25 13:59:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'В лучшем случае', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 114, 688516706, '2020-04-25 13:59:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Хуясе', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 115, 688516706, '2020-04-25 13:59:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Де?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 116, 179513218, '2020-04-25 14:00:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Упал', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 117, 179513218, '2020-04-25 14:00:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'В футбол играл', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 118, 179513218, '2020-04-25 14:00:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Пздц', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 119, 688516706, '2020-04-25 14:00:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Еба, бухой был?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 120, 179513218, '2020-04-25 14:00:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Не, просто толстый', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 121, 688516706, '2020-04-25 14:00:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Шота помочь, принести?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 122, 179513218, '2020-04-25 14:00:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"width\":512,\"height\":512,\"emoji\":\"\\ud83d\\ude19\",\"set_name\":\"LolAnimals\",\"is_animated\":false,\"thumb\":{\"file_id\":\"AAMCAgADGQEAA3pepGy-uxSMfA9iUR8QG0WUuIRwnQACGAMAAlwCZQOo7LGqXnSlEsT4cA0ABAEAB20AAxM7AAIZBA\",\"file_unique_id\":\"AQADxPhwDQAEEzsAAg\",\"file_size\":2970,\"width\":128,\"height\":128},\"file_id\":\"CAACAgIAAxkBAAN6XqRsvrsUjHwPYlEfEBtFlLiEcJ0AAhgDAAJcAmUDqOyxql50pRIZBA\",\"file_unique_id\":\"AgADGAMAAlwCZQM\",\"file_size\":31360}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 123, 179513218, '2020-04-25 14:00:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Не, норм,', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 124, 179513218, '2020-04-25 14:00:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Как сам', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 125, 688516706, '2020-04-25 14:01:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Вчера приходил в гости пьяный Энд )', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 126, 688516706, '2020-04-25 14:02:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Нажрались и я его потом домой провожал', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 127, 179513218, '2020-04-25 14:02:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Жесть', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 128, 179513218, '2020-04-25 14:02:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Он походу под откос', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 129, 688516706, '2020-04-25 14:02:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Работает на южкабеле по-прежнему', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 130, 179513218, '2020-04-25 14:02:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Идёт', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 131, 179513218, '2020-04-25 14:02:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Я его летом прошлым видел', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 132, 179513218, '2020-04-25 14:02:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Глаза чувак', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 133, 688516706, '2020-04-25 14:02:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'И волонтером с летучими мышами', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 134, 179513218, '2020-04-25 14:02:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ага', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 135, 688516706, '2020-04-25 14:03:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Дачу купили за 5 штук за старым салтовом', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 136, 688516706, '2020-04-25 14:03:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2 участка, дом и земля', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 137, 179513218, '2020-04-25 14:03:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ого', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 138, 179513218, '2020-04-25 14:04:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Значит показалось', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 139, 688516706, '2020-04-25 14:04:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Бабушка у него умерла недавно', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 140, 688516706, '2020-04-25 14:08:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Если шо надо будет - говори, я ж недалеко )', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 141, 179513218, '2020-04-25 14:09:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Окай', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 142, 179513218, '2020-04-25 14:09:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Спасибо', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 152, 179513218, '2020-04-25 14:56:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Твой дом сеть )', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 164, 179513218, '2020-04-25 14:58:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"width\":512,\"height\":512,\"emoji\":\"\\ud83d\\udc4d\",\"set_name\":\"FunkyGoose\",\"is_animated\":true,\"thumb\":{\"file_id\":\"AAMCAgADGQEAA6RepHoxqW5INHCPfN_PoiPhoLio0wACRgADUomRI_j-5eQK1QodMQABww8ABAEAB20AA_I2AAIZBA\",\"file_unique_id\":\"AQADMQABww8ABPI2AAI\",\"file_size\":5372,\"width\":128,\"height\":128},\"file_id\":\"CAACAgIAAxkBAAOkXqR6MaluSDRwj3zfz6Ij4aC4qNMAAkYAA1KJkSP4_uXkCtUKHRkE\",\"file_unique_id\":\"AgADRgADUomRIw\",\"file_size\":10245}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 171, 688516706, '2020-04-25 14:59:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/hui', '[{\"offset\":0,\"length\":4,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 214, 688516706, '2020-04-26 19:38:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/corona', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 220, 688516706, '2020-04-27 15:28:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/corona', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 222, 688516706, '2020-04-27 15:28:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/currency', '[{\"offset\":0,\"length\":9,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(-418078787, 244, 688516706, '2020-04-28 06:48:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 1, 688516706, '2020-04-23 19:43:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/start', '[{\"offset\":0,\"length\":6,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 2, 688516706, '2020-04-23 19:44:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/1', '[{\"offset\":0,\"length\":2,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 3, 688516706, '2020-04-24 06:24:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/whoami', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 6, 688516706, '2020-04-24 06:48:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/start', '[{\"offset\":0,\"length\":6,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 7, 688516706, '2020-04-24 08:26:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/start', '[{\"offset\":0,\"length\":6,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 9, 688516706, '2020-04-24 08:26:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/help', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 11, 688516706, '2020-04-24 08:27:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/whoami', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 13, 688516706, '2020-04-24 08:29:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/help', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 15, 688516706, '2020-04-24 08:30:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/markdown aa', '[{\"offset\":0,\"length\":9,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 17, 688516706, '2020-04-24 08:30:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/markdown bold hi there', '[{\"offset\":0,\"length\":9,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 19, 688516706, '2020-04-24 08:31:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/help markdown', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 21, 688516706, '2020-04-24 08:57:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/currency', '[{\"offset\":0,\"length\":9,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 48, 688516706, '2020-04-25 09:26:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Привет', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 49, 688516706, '2020-04-25 09:27:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Как дела', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 50, 688516706, '2020-04-25 09:28:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/start', '[{\"offset\":0,\"length\":6,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 52, 688516706, '2020-04-25 10:07:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Привет', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 53, 688516706, '2020-04-25 11:01:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Привет', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 54, 688516706, '2020-04-25 11:07:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ку', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 56, 688516706, '2020-04-25 11:09:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Привет', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 58, 688516706, '2020-04-25 11:12:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Привет!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 60, 688516706, '2020-04-25 11:12:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Как дела?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 74, 688516706, '2020-04-25 11:17:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Какие новости?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 75, 688516706, '2020-04-25 11:17:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Как дела?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 77, 688516706, '2020-04-25 11:17:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Постой', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 79, 688516706, '2020-04-25 11:17:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Чем занимаешься?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 81, 688516706, '2020-04-25 11:18:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'А что такое робот?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 83, 688516706, '2020-04-25 11:20:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Погода', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 85, 688516706, '2020-04-25 11:20:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Какая погода в Харькове?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 87, 688516706, '2020-04-25 11:21:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Чего приутих?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 88, 688516706, '2020-04-25 11:21:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Молчишь?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 89, 688516706, '2020-04-25 11:24:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Молчишь?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 90, 688516706, '2020-04-25 11:25:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Молчишь?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 91, 688516706, '2020-04-25 11:25:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Какая погода?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 93, 688516706, '2020-04-25 11:26:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Можешь помочь?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 95, 688516706, '2020-04-25 11:26:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Тогда помоги мне', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 97, 688516706, '2020-04-25 11:27:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Какой курс валют?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 98, 688516706, '2020-04-25 11:27:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ты не в курсе?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 100, 688516706, '2020-04-25 11:27:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Чему ты учишься?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 102, 688516706, '2020-04-25 11:59:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Погода', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 104, 688516706, '2020-04-25 11:59:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Погода Харьков', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 106, 688516706, '2020-04-25 11:59:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Курс', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 107, 688516706, '2020-04-25 12:00:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Курс', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 108, 688516706, '2020-04-25 12:09:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/weather Kharkiv', '[{\"offset\":0,\"length\":8,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 110, 688516706, '2020-04-25 12:17:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/weather', '[{\"offset\":0,\"length\":8,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 153, 688516706, '2020-04-25 14:57:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 162, 688516706, '2020-04-25 14:57:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 165, 688516706, '2020-04-25 14:58:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Харьков', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 167, 688516706, '2020-04-25 14:59:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Ukraine', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 169, 688516706, '2020-04-25 14:59:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/currency', '[{\"offset\":0,\"length\":9,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 173, 688516706, '2020-04-25 15:01:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 175, 688516706, '2020-04-25 15:02:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 177, 688516706, '2020-04-25 15:04:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 179, 688516706, '2020-04-25 15:09:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 181, 688516706, '2020-04-25 15:10:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 183, 688516706, '2020-04-25 17:17:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 184, 688516706, '2020-04-25 17:23:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 185, 688516706, '2020-04-25 17:30:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 186, 688516706, '2020-04-25 17:46:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 187, 688516706, '2020-04-25 17:48:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 188, 688516706, '2020-04-25 18:07:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 190, 688516706, '2020-04-25 18:24:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 192, 688516706, '2020-04-25 20:17:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Kharkiv', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 194, 688516706, '2020-04-26 08:26:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Sharm-el-sheikh', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 196, 688516706, '2020-04-26 08:26:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Moscow', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 198, 688516706, '2020-04-26 08:27:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/date Vienna', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 200, 688516706, '2020-04-26 08:27:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/hui', '[{\"offset\":0,\"length\":4,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 202, 688516706, '2020-04-26 08:28:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Привет!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 204, 688516706, '2020-04-26 08:28:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Как дела?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 206, 688516706, '2020-04-26 08:29:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Что делаешь?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 208, 688516706, '2020-04-26 12:30:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/currency', '[{\"offset\":0,\"length\":9,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 210, 688516706, '2020-04-26 18:56:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/corona', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 211, 688516706, '2020-04-26 18:58:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/corona', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 212, 688516706, '2020-04-26 19:37:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/corona', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 216, 688516706, '2020-04-27 09:25:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/corona', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 218, 688516706, '2020-04-27 12:54:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/corona', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 224, 688516706, '2020-04-27 20:16:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `message` (`chat_id`, `id`, `user_id`, `date`, `forward_from`, `forward_from_chat`, `forward_from_message_id`, `forward_signature`, `forward_sender_name`, `forward_date`, `reply_to_chat`, `reply_to_message`, `edit_date`, `media_group_id`, `author_signature`, `text`, `entities`, `caption_entities`, `audio`, `document`, `animation`, `game`, `photo`, `sticker`, `video`, `voice`, `video_note`, `caption`, `contact`, `location`, `venue`, `poll`, `dice`, `new_chat_members`, `left_chat_member`, `new_chat_title`, `new_chat_photo`, `delete_chat_photo`, `group_chat_created`, `supergroup_chat_created`, `channel_chat_created`, `migrate_to_chat_id`, `migrate_from_chat_id`, `pinned_message`, `invoice`, `successful_payment`, `connected_website`, `passport_data`, `reply_markup`) VALUES
(688516706, 226, 688516706, '2020-04-27 20:18:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 228, 688516706, '2020-04-27 20:18:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 230, 688516706, '2020-04-27 20:19:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 232, 688516706, '2020-04-27 20:20:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 234, 688516706, '2020-04-27 20:22:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 236, 688516706, '2020-04-27 20:57:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 238, 688516706, '2020-04-27 20:58:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 240, 688516706, '2020-04-27 21:22:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 241, 688516706, '2020-04-27 21:32:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 242, 688516706, '2020-04-27 21:33:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 243, 688516706, '2020-04-27 21:36:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/anec', '[{\"offset\":0,\"length\":5,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 246, 688516706, '2020-04-28 08:05:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/currency', '[{\"offset\":0,\"length\":9,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(688516706, 248, 688516706, '2020-04-28 08:06:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/corona', '[{\"offset\":0,\"length\":7,\"type\":\"bot_command\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `poll`
--

CREATE TABLE `poll` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique poll identifier',
  `question` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Poll question',
  `options` text COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'List of poll options',
  `total_voter_count` int(10) UNSIGNED DEFAULT NULL COMMENT 'Total number of users that voted in the poll',
  `is_closed` tinyint(1) DEFAULT '0' COMMENT 'True, if the poll is closed',
  `is_anonymous` tinyint(1) DEFAULT '1' COMMENT 'True, if the poll is anonymous',
  `type` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Poll type, currently can be “regular” or “quiz”',
  `allows_multiple_answers` tinyint(1) DEFAULT '0' COMMENT 'True, if the poll allows multiple answers',
  `correct_option_id` int(10) UNSIGNED DEFAULT NULL COMMENT '0-based identifier of the correct answer option. Available only for polls in the quiz mode, which are closed, or was sent (not forwarded) by the bot or to the private chat with the bot.',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_answer`
--

CREATE TABLE `poll_answer` (
  `poll_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique poll identifier',
  `user_id` bigint(20) NOT NULL COMMENT 'The user, who changed the answer to the poll',
  `option_ids` text COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '0-based identifiers of answer options, chosen by the user. May be empty if the user retracted their vote.',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pre_checkout_query`
--

CREATE TABLE `pre_checkout_query` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique query identifier',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'User who sent the query',
  `currency` char(3) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Three-letter ISO 4217 currency code',
  `total_amount` bigint(20) DEFAULT NULL COMMENT 'Total price in the smallest units of the currency',
  `invoice_payload` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'Bot specified invoice payload',
  `shipping_option_id` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Identifier of the shipping option chosen by the user',
  `order_info` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Order info provided by the user',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_limiter`
--

CREATE TABLE `request_limiter` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique identifier for this entry',
  `chat_id` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Unique chat identifier',
  `inline_message_id` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Identifier of the sent inline message',
  `method` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Request method',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `request_limiter`
--

INSERT INTO `request_limiter` (`id`, `chat_id`, `inline_message_id`, `method`, `created_at`) VALUES
(1, '688516706', NULL, 'sendMessage', '2020-04-24 08:26:06'),
(2, '688516706', NULL, 'sendMessage', '2020-04-24 08:26:16'),
(3, '688516706', NULL, 'sendPhoto', '2020-04-24 08:26:47'),
(4, '688516706', NULL, 'sendMessage', '2020-04-24 08:29:03'),
(5, '688516706', NULL, 'sendMessage', '2020-04-24 08:29:34'),
(6, '688516706', NULL, 'sendMessage', '2020-04-24 08:30:10'),
(7, '688516706', NULL, 'sendMessage', '2020-04-24 08:30:35'),
(8, '688516706', NULL, 'sendMessage', '2020-04-24 08:56:45'),
(9, '-418078787', NULL, 'sendMessage', '2020-04-24 09:15:57'),
(10, '-418078787', NULL, 'sendMessage', '2020-04-24 09:16:32'),
(11, '-418078787', NULL, 'sendMessage', '2020-04-24 09:25:09'),
(12, '-418078787', NULL, 'sendMessage', '2020-04-24 09:31:41'),
(13, '-418078787', NULL, 'sendMessage', '2020-04-24 09:32:17'),
(14, '-418078787', NULL, 'sendMessage', '2020-04-24 09:34:06'),
(15, '-418078787', NULL, 'sendMessage', '2020-04-24 09:34:27'),
(16, '-418078787', NULL, 'sendMessage', '2020-04-24 09:42:31'),
(17, '-418078787', NULL, 'sendMessage', '2020-04-24 11:36:35'),
(18, '688516706', NULL, 'sendMessage', '2020-04-25 09:27:54'),
(19, '688516706', NULL, 'sendMessage', '2020-04-25 11:07:23'),
(20, '688516706', NULL, 'sendMessage', '2020-04-25 11:09:02'),
(21, '688516706', NULL, 'sendMessage', '2020-04-25 11:11:57'),
(22, '688516706', NULL, 'sendMessage', '2020-04-25 11:12:18'),
(23, '-418078787', NULL, 'sendMessage', '2020-04-25 11:12:44'),
(24, '-418078787', NULL, 'sendMessage', '2020-04-25 11:12:55'),
(25, '-418078787', NULL, 'sendMessage', '2020-04-25 11:13:12'),
(26, '-418078787', NULL, 'sendMessage', '2020-04-25 11:13:28'),
(27, '-418078787', NULL, 'sendMessage', '2020-04-25 11:13:49'),
(28, '-418078787', NULL, 'sendMessage', '2020-04-25 11:16:04'),
(29, '688516706', NULL, 'sendMessage', '2020-04-25 11:16:40'),
(30, '688516706', NULL, 'sendMessage', '2020-04-25 11:16:51'),
(31, '688516706', NULL, 'sendMessage', '2020-04-25 11:17:13'),
(32, '688516706', NULL, 'sendMessage', '2020-04-25 11:17:29'),
(33, '688516706', NULL, 'sendMessage', '2020-04-25 11:17:45'),
(34, '688516706', NULL, 'sendMessage', '2020-04-25 11:20:08'),
(35, '688516706', NULL, 'sendMessage', '2020-04-25 11:20:24'),
(36, '688516706', NULL, 'sendMessage', '2020-04-25 11:20:45'),
(37, '688516706', NULL, 'sendMessage', '2020-04-25 11:21:01'),
(38, '688516706', NULL, 'sendMessage', '2020-04-25 11:25:05'),
(39, '688516706', NULL, 'sendMessage', '2020-04-25 11:25:26'),
(40, '688516706', NULL, 'sendMessage', '2020-04-25 11:25:47'),
(41, '688516706', NULL, 'sendMessage', '2020-04-25 11:26:03'),
(42, '688516706', NULL, 'sendMessage', '2020-04-25 11:26:30'),
(43, '688516706', NULL, 'sendMessage', '2020-04-25 11:26:56'),
(44, '688516706', NULL, 'sendMessage', '2020-04-25 11:27:17'),
(45, '688516706', NULL, 'sendMessage', '2020-04-25 11:58:46'),
(46, '688516706', NULL, 'sendMessage', '2020-04-25 11:59:02'),
(47, '688516706', NULL, 'sendMessage', '2020-04-25 11:59:14'),
(48, '688516706', NULL, 'sendMessage', '2020-04-25 12:08:46'),
(49, '688516706', NULL, 'sendMessage', '2020-04-25 12:16:57'),
(50, '-418078787', NULL, 'sendMessage', '2020-04-25 14:55:53'),
(51, '-418078787', NULL, 'sendMessage', '2020-04-25 14:55:54'),
(52, '-418078787', NULL, 'sendMessage', '2020-04-25 14:55:55'),
(53, '-418078787', NULL, 'sendMessage', '2020-04-25 14:55:56'),
(54, '-418078787', NULL, 'sendMessage', '2020-04-25 14:55:57'),
(55, '-418078787', NULL, 'sendMessage', '2020-04-25 14:55:58'),
(56, '-418078787', NULL, 'sendMessage', '2020-04-25 14:55:59'),
(57, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:00'),
(58, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:01'),
(59, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:02'),
(60, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:03'),
(61, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:04'),
(62, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:05'),
(63, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:06'),
(64, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:07'),
(65, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:08'),
(66, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:09'),
(67, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:10'),
(68, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:11'),
(69, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:12'),
(70, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:54'),
(71, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:55'),
(72, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:56'),
(73, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:57'),
(74, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:58'),
(75, '-418078787', NULL, 'sendMessage', '2020-04-25 14:56:59'),
(76, '-418078787', NULL, 'sendMessage', '2020-04-25 14:57:00'),
(77, '-418078787', NULL, 'sendMessage', '2020-04-25 14:57:01'),
(78, '-418078787', NULL, 'sendMessage', '2020-04-25 14:57:02'),
(79, '-418078787', NULL, 'sendMessage', '2020-04-25 14:57:03'),
(80, '688516706', NULL, 'sendMessage', '2020-04-25 14:57:08'),
(81, '-418078787', NULL, 'sendMessage', '2020-04-25 14:57:09'),
(82, '688516706', NULL, 'sendMessage', '2020-04-25 14:57:24'),
(83, '688516706', NULL, 'sendMessage', '2020-04-25 14:57:50'),
(84, '688516706', NULL, 'sendMessage', '2020-04-25 14:58:31'),
(85, '688516706', NULL, 'sendMessage', '2020-04-25 14:58:42'),
(86, '-418078787', NULL, 'sendMessage', '2020-04-25 14:59:12'),
(87, '688516706', NULL, 'sendMessage', '2020-04-25 15:01:15'),
(88, '688516706', NULL, 'sendMessage', '2020-04-25 15:01:30'),
(89, '688516706', NULL, 'sendMessage', '2020-04-25 15:04:11'),
(90, '688516706', NULL, 'sendMessage', '2020-04-25 15:08:31'),
(91, '688516706', NULL, 'sendMessage', '2020-04-25 15:10:07'),
(92, '688516706', NULL, 'sendMessage', '2020-04-25 18:06:49'),
(93, '688516706', NULL, 'sendMessage', '2020-04-25 18:23:46'),
(94, '688516706', NULL, 'sendMessage', '2020-04-25 20:17:19'),
(95, '688516706', NULL, 'sendMessage', '2020-04-26 08:25:50'),
(96, '688516706', NULL, 'sendMessage', '2020-04-26 08:26:11'),
(97, '688516706', NULL, 'sendMessage', '2020-04-26 08:26:52'),
(98, '688516706', NULL, 'sendMessage', '2020-04-26 08:27:32'),
(99, '688516706', NULL, 'sendMessage', '2020-04-26 08:28:15'),
(100, '688516706', NULL, 'sendMessage', '2020-04-26 08:28:36'),
(101, '688516706', NULL, 'sendMessage', '2020-04-26 08:29:17'),
(102, '688516706', NULL, 'sendMessage', '2020-04-26 18:55:37'),
(103, '688516706', NULL, 'sendMessage', '2020-04-26 19:37:13'),
(104, '-418078787', NULL, 'sendMessage', '2020-04-26 19:37:50'),
(105, '688516706', NULL, 'sendMessage', '2020-04-27 09:24:37'),
(106, '688516706', NULL, 'sendMessage', '2020-04-27 12:53:55'),
(107, '-418078787', NULL, 'sendMessage', '2020-04-27 15:27:49'),
(108, '-418078787', NULL, 'sendMessage', '2020-04-27 15:28:20'),
(109, '688516706', NULL, 'sendMessage', '2020-04-27 20:16:06'),
(110, '688516706', NULL, 'sendMessage', '2020-04-27 20:17:30'),
(111, '688516706', NULL, 'sendMessage', '2020-04-27 20:18:11'),
(112, '688516706', NULL, 'sendMessage', '2020-04-27 20:19:07'),
(113, '688516706', NULL, 'sendMessage', '2020-04-27 20:20:14'),
(114, '688516706', NULL, 'sendMessage', '2020-04-27 20:21:34'),
(115, '688516706', NULL, 'sendMessage', '2020-04-27 20:57:01'),
(116, '688516706', NULL, 'sendMessage', '2020-04-27 20:58:02'),
(117, '-418078787', NULL, 'sendMessage', '2020-04-28 06:47:27'),
(118, '688516706', NULL, 'sendMessage', '2020-04-28 08:05:19'),
(119, '688516706', NULL, 'sendMessage', '2020-04-28 08:05:41');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_query`
--

CREATE TABLE `shipping_query` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unique query identifier',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'User who sent the query',
  `invoice_payload` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'Bot specified invoice payload',
  `shipping_address` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'User specified shipping address',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telegram_update`
--

CREATE TABLE `telegram_update` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Update''s unique identifier',
  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
  `message_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming message of any kind - text, photo, sticker, etc.',
  `edited_message_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New version of a message that is known to the bot and was edited',
  `channel_post_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming channel post of any kind - text, photo, sticker, etc.',
  `edited_channel_post_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New version of a channel post that is known to the bot and was edited',
  `inline_query_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming inline query',
  `chosen_inline_result_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'The result of an inline query that was chosen by a user and sent to their chat partner',
  `callback_query_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming callback query',
  `shipping_query_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming shipping query. Only for invoices with flexible price',
  `pre_checkout_query_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New incoming pre-checkout query. Contains full information about checkout',
  `poll_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'New poll state. Bots receive only updates about polls, which are sent or stopped by the bot',
  `poll_answer_poll_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'A user changed their answer in a non-anonymous poll. Bots receive new votes only in polls that were sent by the bot itself.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `telegram_update`
--

INSERT INTO `telegram_update` (`id`, `chat_id`, `message_id`, `edited_message_id`, `channel_post_id`, `edited_channel_post_id`, `inline_query_id`, `chosen_inline_result_id`, `callback_query_id`, `shipping_query_id`, `pre_checkout_query_id`, `poll_id`, `poll_answer_poll_id`) VALUES
(286830506, 688516706, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830507, 688516706, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830508, 688516706, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830509, 688516706, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830510, 688516706, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830511, 688516706, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830512, 688516706, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830513, 688516706, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830514, 688516706, 15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830515, 688516706, 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830516, 688516706, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830517, 688516706, 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830518, -418078787, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830519, -418078787, 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830520, -418078787, 26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830521, -418078787, 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830522, -418078787, 28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830523, -418078787, 29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830524, -418078787, 31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830525, -418078787, 33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830526, -418078787, 35, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830527, -418078787, 37, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830528, -418078787, 39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830529, -418078787, 40, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830530, -418078787, 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830531, -418078787, 43, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830532, -418078787, 44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830533, -418078787, 46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830534, 688516706, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830535, 688516706, 49, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830536, 688516706, 50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830537, 688516706, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830538, 688516706, 53, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830539, 688516706, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830540, 688516706, 56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830541, 688516706, 58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830542, 688516706, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830543, -418078787, 62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830544, -418078787, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830545, -418078787, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830546, -418078787, 68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830547, -418078787, 70, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830548, -418078787, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830549, -418078787, 73, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830550, 688516706, 74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830551, 688516706, 75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830552, 688516706, 77, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830553, 688516706, 79, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830554, 688516706, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830555, 688516706, 83, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830556, 688516706, 85, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830557, 688516706, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830558, 688516706, 88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830559, 688516706, 89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830560, 688516706, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830561, 688516706, 91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830562, 688516706, 93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830563, 688516706, 95, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830564, 688516706, 97, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830565, 688516706, 98, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830566, 688516706, 100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830567, 688516706, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830568, 688516706, 104, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830569, 688516706, 106, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830570, 688516706, 107, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830571, 688516706, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830572, 688516706, 110, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830573, -418078787, 112, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830574, -418078787, 113, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830575, -418078787, 114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830576, -418078787, 115, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830577, -418078787, 116, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830578, -418078787, 117, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830579, -418078787, 118, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830580, -418078787, 119, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830581, -418078787, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830582, -418078787, 121, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830583, -418078787, 122, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830584, -418078787, 123, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830585, -418078787, 124, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830586, -418078787, 125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830587, -418078787, 126, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830588, -418078787, 127, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830589, -418078787, 128, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830590, -418078787, 129, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830591, -418078787, 130, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830592, -418078787, 131, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830593, -418078787, 132, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830594, -418078787, 133, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830595, -418078787, 134, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830596, -418078787, 135, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830597, -418078787, 136, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830598, -418078787, 137, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830599, -418078787, 138, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830600, -418078787, 139, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830601, -418078787, 140, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830602, -418078787, 141, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830603, -418078787, 142, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830604, -418078787, 152, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830605, 688516706, 153, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830606, 688516706, 162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830607, -418078787, 164, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830608, 688516706, 165, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830609, 688516706, 167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830610, 688516706, 169, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830611, -418078787, 171, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830612, 688516706, 173, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830613, 688516706, 175, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830614, 688516706, 177, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830615, 688516706, 179, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830616, 688516706, 181, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830617, 688516706, 183, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830618, 688516706, 184, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830619, 688516706, 185, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830620, 688516706, 186, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830621, 688516706, 187, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830622, 688516706, 188, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830623, 688516706, 190, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830624, 688516706, 192, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830625, 688516706, 194, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830626, 688516706, 196, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830627, 688516706, 198, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830628, 688516706, 200, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830629, 688516706, 202, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830630, 688516706, 204, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830631, 688516706, 206, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830632, 688516706, 208, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830633, 688516706, 210, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830634, 688516706, 211, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830635, 688516706, 212, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830636, -418078787, 214, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830637, 688516706, 216, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830638, 688516706, 218, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830639, -418078787, 220, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830640, -418078787, 222, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830641, 688516706, 224, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830642, 688516706, 226, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830643, 688516706, 228, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830644, 688516706, 230, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830645, 688516706, 232, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830646, 688516706, 234, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830647, 688516706, 236, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830648, 688516706, 238, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830649, 688516706, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830650, 688516706, 241, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830651, 688516706, 242, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830652, 688516706, 243, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830653, -418078787, 244, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830654, 688516706, 246, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286830655, 688516706, 248, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL COMMENT 'Unique identifier for this user or bot',
  `is_bot` tinyint(1) DEFAULT '0' COMMENT 'True, if this user is a bot',
  `first_name` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'User''s or bot''s first name',
  `last_name` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'User''s or bot''s last name',
  `username` char(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'User''s or bot''s username',
  `language_code` char(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'IETF language tag of the user''s language',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `is_bot`, `first_name`, `last_name`, `username`, `language_code`, `created_at`, `updated_at`) VALUES
(179513218, 0, 'Артур', NULL, 'Artweb7', NULL, '2020-04-24 09:16:58', '2020-04-25 14:58:09'),
(688516706, 0, 'Олег', NULL, 'olegfreedom777', 'ru', '2020-04-23 19:43:14', '2020-04-28 08:06:10'),
(1164525105, 1, 'Buddy', NULL, 'FreedomBuddyBot', NULL, '2020-04-24 09:15:59', '2020-04-24 09:15:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_chat`
--

CREATE TABLE `user_chat` (
  `user_id` bigint(20) NOT NULL COMMENT 'Unique user identifier',
  `chat_id` bigint(20) NOT NULL COMMENT 'Unique user or chat identifier'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `user_chat`
--

INSERT INTO `user_chat` (`user_id`, `chat_id`) VALUES
(179513218, -418078787),
(688516706, -418078787),
(1164525105, -418078787),
(688516706, 688516706);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anecdot`
--
ALTER TABLE `anecdot`
  ADD PRIMARY KEY (`id`,`description`);

--
-- Indexes for table `callback_query`
--
ALTER TABLE `callback_query`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `chat_id_2` (`chat_id`,`message_id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `old_id` (`old_id`);

--
-- Indexes for table `chosen_inline_result`
--
ALTER TABLE `chosen_inline_result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `conversation`
--
ALTER TABLE `conversation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `edited_message`
--
ALTER TABLE `edited_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chat_id_2` (`chat_id`,`message_id`);

--
-- Indexes for table `inline_query`
--
ALTER TABLE `inline_query`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`chat_id`,`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `forward_from` (`forward_from`),
  ADD KEY `forward_from_chat` (`forward_from_chat`),
  ADD KEY `reply_to_chat` (`reply_to_chat`),
  ADD KEY `reply_to_message` (`reply_to_message`),
  ADD KEY `left_chat_member` (`left_chat_member`),
  ADD KEY `migrate_from_chat_id` (`migrate_from_chat_id`),
  ADD KEY `migrate_to_chat_id` (`migrate_to_chat_id`),
  ADD KEY `reply_to_chat_2` (`reply_to_chat`,`reply_to_message`);

--
-- Indexes for table `poll`
--
ALTER TABLE `poll`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_answer`
--
ALTER TABLE `poll_answer`
  ADD PRIMARY KEY (`poll_id`);

--
-- Indexes for table `pre_checkout_query`
--
ALTER TABLE `pre_checkout_query`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `request_limiter`
--
ALTER TABLE `request_limiter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipping_query`
--
ALTER TABLE `shipping_query`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `telegram_update`
--
ALTER TABLE `telegram_update`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `chat_message_id` (`chat_id`,`message_id`),
  ADD KEY `edited_message_id` (`edited_message_id`),
  ADD KEY `channel_post_id` (`channel_post_id`),
  ADD KEY `edited_channel_post_id` (`edited_channel_post_id`),
  ADD KEY `inline_query_id` (`inline_query_id`),
  ADD KEY `chosen_inline_result_id` (`chosen_inline_result_id`),
  ADD KEY `callback_query_id` (`callback_query_id`),
  ADD KEY `shipping_query_id` (`shipping_query_id`),
  ADD KEY `pre_checkout_query_id` (`pre_checkout_query_id`),
  ADD KEY `poll_id` (`poll_id`),
  ADD KEY `poll_answer_poll_id` (`poll_answer_poll_id`),
  ADD KEY `chat_id` (`chat_id`,`channel_post_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `user_chat`
--
ALTER TABLE `user_chat`
  ADD PRIMARY KEY (`user_id`,`chat_id`),
  ADD KEY `chat_id` (`chat_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chosen_inline_result`
--
ALTER TABLE `chosen_inline_result`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry';

--
-- AUTO_INCREMENT for table `conversation`
--
ALTER TABLE `conversation`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry';

--
-- AUTO_INCREMENT for table `edited_message`
--
ALTER TABLE `edited_message`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry';

--
-- AUTO_INCREMENT for table `request_limiter`
--
ALTER TABLE `request_limiter`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry', AUTO_INCREMENT=120;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `callback_query`
--
ALTER TABLE `callback_query`
  ADD CONSTRAINT `callback_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `callback_query_ibfk_2` FOREIGN KEY (`chat_id`,`message_id`) REFERENCES `message` (`chat_id`, `id`);

--
-- Constraints for table `chosen_inline_result`
--
ALTER TABLE `chosen_inline_result`
  ADD CONSTRAINT `chosen_inline_result_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `conversation`
--
ALTER TABLE `conversation`
  ADD CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `conversation_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`);

--
-- Constraints for table `edited_message`
--
ALTER TABLE `edited_message`
  ADD CONSTRAINT `edited_message_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`),
  ADD CONSTRAINT `edited_message_ibfk_2` FOREIGN KEY (`chat_id`,`message_id`) REFERENCES `message` (`chat_id`, `id`),
  ADD CONSTRAINT `edited_message_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `inline_query`
--
ALTER TABLE `inline_query`
  ADD CONSTRAINT `inline_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`),
  ADD CONSTRAINT `message_ibfk_3` FOREIGN KEY (`forward_from`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `message_ibfk_4` FOREIGN KEY (`forward_from_chat`) REFERENCES `chat` (`id`),
  ADD CONSTRAINT `message_ibfk_5` FOREIGN KEY (`reply_to_chat`,`reply_to_message`) REFERENCES `message` (`chat_id`, `id`),
  ADD CONSTRAINT `message_ibfk_6` FOREIGN KEY (`forward_from`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `message_ibfk_7` FOREIGN KEY (`left_chat_member`) REFERENCES `user` (`id`);

--
-- Constraints for table `poll_answer`
--
ALTER TABLE `poll_answer`
  ADD CONSTRAINT `poll_answer_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `poll` (`id`);

--
-- Constraints for table `pre_checkout_query`
--
ALTER TABLE `pre_checkout_query`
  ADD CONSTRAINT `pre_checkout_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `shipping_query`
--
ALTER TABLE `shipping_query`
  ADD CONSTRAINT `shipping_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `telegram_update`
--
ALTER TABLE `telegram_update`
  ADD CONSTRAINT `telegram_update_ibfk_1` FOREIGN KEY (`chat_id`,`message_id`) REFERENCES `message` (`chat_id`, `id`),
  ADD CONSTRAINT `telegram_update_ibfk_10` FOREIGN KEY (`poll_id`) REFERENCES `poll` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_11` FOREIGN KEY (`poll_answer_poll_id`) REFERENCES `poll_answer` (`poll_id`),
  ADD CONSTRAINT `telegram_update_ibfk_2` FOREIGN KEY (`edited_message_id`) REFERENCES `edited_message` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_3` FOREIGN KEY (`chat_id`,`channel_post_id`) REFERENCES `message` (`chat_id`, `id`),
  ADD CONSTRAINT `telegram_update_ibfk_4` FOREIGN KEY (`edited_channel_post_id`) REFERENCES `edited_message` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_5` FOREIGN KEY (`inline_query_id`) REFERENCES `inline_query` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_6` FOREIGN KEY (`chosen_inline_result_id`) REFERENCES `chosen_inline_result` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_7` FOREIGN KEY (`callback_query_id`) REFERENCES `callback_query` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_8` FOREIGN KEY (`shipping_query_id`) REFERENCES `shipping_query` (`id`),
  ADD CONSTRAINT `telegram_update_ibfk_9` FOREIGN KEY (`pre_checkout_query_id`) REFERENCES `pre_checkout_query` (`id`);

--
-- Constraints for table `user_chat`
--
ALTER TABLE `user_chat`
  ADD CONSTRAINT `user_chat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_chat_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
