--
-- Структура таблицы `anecdot`
--

CREATE TABLE `anecdot` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `anecdot`
--
ALTER TABLE `anecdot`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `anecdot`
--
ALTER TABLE `anecdot`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;