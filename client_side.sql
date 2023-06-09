CREATE TABLE `donate_unitpay` (
  `id` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `operator` varchar(20) NOT NULL DEFAULT '',
  `paymentType` varchar(20) NOT NULL DEFAULT '',
  `phone` varchar(25) NOT NULL DEFAULT '',
  `out_summ` float(16,2) NOT NULL,
  `profit` float(16,2) NOT NULL DEFAULT '0.00',
  `unitpayId` int(11) NOT NULL DEFAULT '0',
  `don_kurs` float(16,2) NOT NULL,
  `money` int(11) NOT NULL,
  `act_bonus` int(11) NOT NULL,
  `bonus_money` int(11) NOT NULL,
  `login` varchar(25) CHARACTER SET utf8 NOT NULL,
  `userid` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `serv` int(11) NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '0',
  `errMsg` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `donate_unitpay`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `donate_unitpay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `online_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `online_acc` int(11) NOT NULL,
  `online_pers` int(11) NOT NULL,
  `online_world` int(11) NOT NULL,
  `online_instance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop_icons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `icon` blob NOT NULL,
  UNIQUE KEY `name` (`name`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `donate_freekassa` (
  `id` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(45) NOT NULL DEFAULT '',
  `out_summ` float(16,2) NOT NULL,
  `intid` int(11) NOT NULL DEFAULT '0',
  `don_kurs` float(16,2) NOT NULL,
  `money` int(11) NOT NULL,
  `act_bonus` int(11) NOT NULL,
  `bonus_money` int(11) NOT NULL,
  `login` varchar(25) CHARACTER SET utf8 NOT NULL,
  `userid` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `serv` int(11) NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `donate_freekassa`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `donate_freekassa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'int',
  `value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `section` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `desc` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `config` (`name`, `type`, `value`, `section`, `group`, `desc`) VALUES
('lk_active_for_all', 'bool', '1', 0, 0, 'Открыть / закрыть функции ЛК для всех кроме админов'),
('server_side_script_path', 'string', 'http://localhost/lkab/', 0, 0, 'Путь к папке со скриптами сервер части'),
('auth_type', 'int', '2', 0, 0, 'Способ хранения паролей аккаунтов (используется для авторизации и регистрации):\r\n	1 - binary MD5, пример: <font color=\"#0000ff\">åÅÍOXÖööëtRúýèk</font>\r\n	2 - 0xMD5, пример: <font color=\"#0000ff\">0x671298fb29179832ab3912</font>\r\n	3 - base64(MD5), пример: <font color=\"#0000ff\">w6XDhcONT1jDlsO2FcO2w6t0UsO6w73DqGs=</font>'),
('email_require', 'bool', '0', 0, 0, 'Требовать E-mail для авторизации в ЛК'),
('email_require_change', 'bool', '1', 0, 0, 'Требовать E-mail для смены пароля'),
('answer_require_change', 'bool', '1', 0, 0, 'Требовать Ответ на вопрос для смены пароля'),
('cookie_pasw', 'string', 'GnlweumrfYtbf2fnYN917', 0, 0, 'Пароль для шифрования данных'),
('encoder_Salt', 'string', 'dFEmuwef342', 0, 0, 'Добавочный код для шифрования (соль)'),
('cookie_name', 'string', 'RememberLK', 0, 0, 'Название переменной в cookies'),
('title', 'string', 'Личный кабинет - MyServ', 0, 0, 'Титул страницы'),
('description', 'string', 'Личный кабинет сервера MyServ', 0, 0, 'Описание'),
('uploaddir', 'string', 'uploads', 0, 0, 'Папка для загрузки значков клана игроками'),
('servid', 'int', '1', 0, 0, 'ID сервера в serverlist.txt (для значков клана, доната и т.п.)'),
('default_icon_num', 'int', '0', 0, 0, 'Номер иконки по умолчанию (для значков клана)'),
('favicon', 'string', 'img/favicon.ico', 0, 0, 'Иконка сайта'),
('logo', 'string', 'img/logo20.png', 0, 0, 'Логитип'),
('logotext', 'string', 'MyServ', 0, 0, 'Название сервера в логотипе'),
('cite_link', 'string', 'http://myserv.net', 0, 0, 'Ссылка на сайт'),
('contacts', 'text', '<img src=\"img/skype32.png\" border=0> <strong>myserv</strong>', 0, 0, 'HMTM код с контактами'),
('cron_act_passw', 'string', 'FEnouicvmHNJGvr73nW', 0, 0, 'Пароль для вызова скриптов, запускаемых через Cron. Обязательно поменяйте после установки ЛК!'),
('show_result', 'bool', '1', 0, 0, 'Вывод ответов сервера (<b>DEBUG</b>), используется для отладки и поиска ошибок. <b>\r\nБудет виден только администраторам (с соотв ID или IP из конфига)</b>'),
('mmotop_link', 'string', '', 0, 0, 'Ссылка на голосование в MMOTOP'),
('qtop_link', 'string', '', 0, 0, 'Ссылка на голосование в Q-TOP'),
('zoneid', 'int', '1', 0, 0, 'Параметр zoneid сервера (используется для нескольких серверов на одной базе аккаунтов)'),
('aid', 'int', '11', 0, 0, 'Параметр aid сервера (используется для нескольких серверов на одной базе аккаунтов)'),
('promo_enabled', 'bool', '1', 0, 0, 'Включить возможность ввода промо кодов и получения за них бонусов. Список и награда за промо коды в таблице <b>promo_codes</b> сервер сайда.'),
('email_confirm', 'bool', '1', 1, 0, 'Требовать подтверждения E-mail при регистрации'),
('ip_max_reg', 'int', '0', 1, 0, 'Ограничение кол-ва регистрации аккаунтов на один IP (0 - без ограничений)'),
('email_max_reg', 'int', '0', 1, 0, 'Ограничение кол-ва регистрации аккаунтов на один EMail (0 - без ограничений)'),
('login_filter', 'string', '/[^0-9a-z_]/', 1, 0, 'Фильтр символов для логина'),
('login_min_len', 'int', '3', 1, 0, 'Минимальное количество символов для логина'),
('login_max_len', 'int', '20', 1, 0, 'Максимальное количество символов для логина'),
('check_rus', 'string', '/^[a-zA-Zа-яА-ЯёЁ0-9\\-\\+_ ]+$/u', 1, 0, 'Фильтр для вопроса/ответа при регистрации и смене пароля'),
('passw_filter', 'string', '/[^0-9a-zA-Z\\-\\+_ ]/', 1, 0, 'Фильтр символов для пароля'),
('passw_min_len', 'int', '6', 1, 0, 'Минимальное количество символов для пароля'),
('passw_max_len', 'int', '20', 1, 0, 'Максимальное количество символов для пароля'),
('quest_template', 'arraystring', 'Ваше любимое блюдо,Девичья фамилия матери,Любимое число,Название любимой книги,Ваш любимый писатель,Кличка животного,Номер телефона,Имя Вашей первой учительницы,Имя и отчество Вашей бабушки,Модель первой машины,Номер телефона друга', 1, 0, 'Список вопросов для регистрации (разделитель запятая без пробелов, длина вопроса не более 25 символов)'),
('rules_link', 'string', 'http://localhost/rules.php/', 1, 0, 'Ссылка на страницу с правилами проекта'),
('register_gold', 'int', '0', 1, 1, 'Количество голда, выдаваемое после регистрации аккаунта (0 - не выдавать)'),
('get_gold_btn', 'bool', '0', 1, 1, 'Выдавать стартовый голд по запросу через личный кабинет (рекомендуется использовать этот способ)'),
('register_active', 'bool', '1', 1, 2, 'Включить/Выключить регистрацию аккаунтов'),
('', 'desc', 'Настройки реферальной системы', 1, 3, ''),
('ref_don_bonus_enable', 'bool', '0', 1, 3, 'Выдавать % от доната за реферала'),
('ref_don_bonus', 'float', '5', 1, 3, 'Размер % от доната приглашенного (поддерживается дробное значение)'),
('ref_don_bonus_timeused', 'int', '360000', 1, 3, 'Кол-во секунд онлайн на приглашенном аккаунте, необходимых для получения % от доната'),
('ref_level_bonus_enabled', 'bool', '0', 1, 3, 'Выдавать предмет за получение уровня у персонажа на приглашенном аккаунте'),
('ref_require_level', 'int', '105', 1, 3, 'Уровень, который должен апнуть приглашенный игрок, для выдачи бонуса пригласившему'),
('ref_require_rb', 'bool', '0', 1, 3, 'Требование наличия реборна у приглашенного игрока, для выдачи бонуса пригласившему'),
('ref_item', 'item', '00000000000000020000000000000000000000000000000000000000090000000a', 1, 3, 'Предмет за достижение уровня (выдается тому, кто пригласил)'),
('', 'desc', 'Авторизация через ВКонтакте <img src=\"img/vk_login.png\">', 1, 4, ''),
('vk_login_enable', 'bool', '0', 1, 4, 'Разрешить авторизацию через ВКонтакте'),
('vk_app_id', 'string', '', 1, 4, 'ID приложения ВКонтакте. Приложение можно <a href=\"https://vk.com/apps?act=manage\" target=\"_blank\">создать тут</a>'),
('vk_app_key', 'string', '', 1, 4, 'Ключ приложения ВКонтакте'),
('', 'desc', 'Авторизация через Steam <img src=\"img/steam_login.png\">', 1, 5, ''),
('steam_login_enable', 'bool', '0', 1, 5, 'Разрешить авторизацию через Steam'),
('steam_app_key', 'string', '', 1, 5, 'Ключ приложения Steam можно <a href=\"http://steamcommunity.com/dev/apikey\" target=\"_blank\">найти тут</a>'),
('allow_lk_transfer', 'bool', '0', 2, 0, 'Разрешить перевод ЛК монет на другой аккаунт'),
('lk_transfer_min_role_lvl', 'int', '0', 2, 0, 'Минимальный уровень персонажа на аккаунте, для возможности перевода ЛК монет на другой аккаунт\r\n	0 - отключить требование'),
('lk_transfer_vk_only', 'bool', '0', 2, 0, 'Разрешить перевод ЛК монет на другой аккаунт, только если привязана учетка VK'),
('lk_transfer_steam_only', 'bool', '0', 2, 0, 'Разрешить перевод ЛК монет на другой аккаунт, только если привязана учетка STEAM'),
('nullbankpass_enable', 'bool', '1', 2, 4, 'Разрешить сброс пароля от банка'),
('nullbankpass', 'cost', '25|0', 2, 4, 'Стоимость обнуления пароля банка'),
('allow_lk_gold_exchange', 'bool', '0', 2, 6, 'Разрешить обмен игровых ресурсов на ЛК голд'),
('gold_itemid', 'itemid', '0', 2, 6, 'ID игрового итема для обмена на ЛК голд'),
('gold_item_exchange_rate', 'int', '1', 2, 6, 'Курс обмена итемов на ЛК голд, 1 ЛК голд = n items'),
('allow_lk_silver_exchange', 'bool', '0', 2, 7, 'Разрешить обмен игровых ресурсов на ЛК серебро'),
('silver_itemid', 'itemid', '0', 2, 7, 'ID игрового итема для обмена на ЛК серебро'),
('silver_item_exchange_rate', 'int', '3', 2, 7, 'Курс обмена итемов на ЛК серебро, 1 ЛК серебро = n items'),
('allow_lk2game', 'bool', '0', 2, 9, 'Разрешить вывод лк монет в игру (голд шоп)'),
('lk2game_exchange_rate', 'float', '1', 2, 9, 'Курс обмена ЛК на голд, 1 ЛК = n голда (поддерживаются дробные значения с отсеканием дробной части)'),
('klancost', 'cost', '10|0', 2, 12, 'Стоимость установки значка клана'),
('klan_pic_size', 'int', '16', 2, 12, 'Размер значка клану в пикселях'),
('act_bonus', 'int', '0', 3, 0, 'Текущий бонус к донату в %'),
('don_kurs', 'float', '1', 3, 0, 'Множитель расчета монет сумма = монеты * множитель'),
('donate_system', 'select', 'Disabled,UnitPay,Free-Kassa,2', 3, 0, 'Используемая платежная система для доната, Disabled - для отключения доната'),
('accumulation_system', 'bool', '1', 3, 0, 'Использовать накопительную систему (бонусы, зависимые от суммы всех платежей)'),
('acc_param', 'arraylist', 'a:9:{i:0;s:1:\"0\";i:500;s:1:\"1\";i:1000;s:1:\"3\";i:3000;s:1:\"5\";i:5000;s:1:\"7\";i:10000;s:2:\"10\";i:15000;s:2:\"13\";i:20000;s:2:\"16\";i:30000;s:2:\"20\";}', 3, 0, 'Параметры накопительной системы:\r\nсумма-бонус\r\nсумма-бонус\r\nи т.п.\r\n\r\nсумма в руб, бонус в %'),
('bonus_system', 'bool', '1', 3, 0, 'Использовать бонусную систему (бонусы, зависимые от суммы единоразового платежа)'),
('bonus_param', 'arraylist', 'a:4:{i:500;s:1:\"3\";i:1000;s:1:\"7\";i:3000;s:2:\"10\";i:5000;s:2:\"15\";}', 3, 0, 'Параметры бонусной системы:\r\nсумма-бонус\r\nсумма-бонус\r\nи т.п.\r\n\r\nсумма в руб, бонус в %'),
('', 'desc', 'Бонусный итем при донате', 3, 0, ''),
('send_bonus_item', 'bool', '1', 3, 0, 'Отправлять бонусный итем при донате'),
('bonus_item', 'item', '000000000000000000000000000000000000000000000000000000000000000000', 3, 0, 'Параметры бонусного итема'),
('', 'desc', 'Параметры сервиса Free-Kassa', 3, 0, ''),
('freekassa_merchant_id', 'int', '0', 3, 0, 'ID проекта в системе FreeKassa'),
('freekassa_secret_word1', 'string', '', 3, 0, 'Секретное слово 1 в системе FreeKassa'),
('freekassa_secret_word2', 'string', '', 3, 0, 'Секретное слово 2 в системе FreeKassa'),
('', 'desc', 'Параметры сервиса UnitPay', 3, 1, ''),
('unitpay_id', 'int', '0', 3, 1, 'ID проекта в системе UnitPay'),
('unitpay_secret_key', 'string', '', 3, 1, 'Секретный ключ проекта в системе UnitPay'),
('unitpay_form_url', 'string', '', 3, 1, 'Ссылка формы оплаты проекта в системе UnitPay, например https://unitpay.ru/pay/1111-11111'),
('max_votes_from_ip', 'int', '2', 4, 0, 'Максимум голосов в сутки с одного айпи'),
('max_votes_from_login', 'int', '2', 4, 0, 'Максимум голосов в сутки на один логин'),
('num_day_process', 'int', '2', 4, 0, 'Количество обрабатываемых дней (будут обрабатываться только голоса давностью не более num дней)'),
('top_bonus_vk_only', 'bool', '0', 4, 0, 'Выдавать бонус за голосование только на аккаунты с привязанным VK'),
('top_bonus_steam_only', 'bool', '0', 4, 0, 'Выдавать бонус за голосование только на аккаунты с привязанным Steam'),
('', 'desc', 'Настройка бонусов за голосование в MMOTOP', 4, 1, ''),
('mmotop_statlink', 'string', '', 4, 1, 'Ссылка на статистику голосов MMOTOP'),
('mmotop_cost1', 'cost', '1|0', 4, 1, 'Стоимость обычного голоса'),
('mmotop_cost2', 'cost', '2|0', 4, 1, 'Стоимость SMS голоса (10 голосов)'),
('mmotop_cost3', 'cost', '3|0', 4, 1, 'Стоимость SMS голоса (50 голосов)'),
('mmotop_cost4', 'cost', '4|0', 4, 1, 'Стоимость SMS голоса (100 голосов)'),
('send_mmotop_message', 'bool', '1', 4, 1, 'Отправлять сообщение в ГМ чат после выдачи бонусов'),
('mmotop_message', 'string', '%d игроков только что получили бонусы за голосование в mmotop. Поддержи проект - получи бонус!', 4, 1, 'Сообщение в ГМ чат после выдачи бонусов'),
('send_mmotop_bonusitem', 'bool', '0', 4, 1, 'Отправлять предмет за голосование'),
('mmotop1_item', 'item', '000000000000000000000000000000000000000000000000000000000000000000', 4, 1, 'Параметры бонусного итема за обычный голос MMOTOP'),
('mmotop2_item', 'item', '000000000000000000000000000000000000000000000000000000000000000000', 4, 1, 'Параметры бонусного итема за SMS (10) голосов MMOTOP'),
('mmotop3_item', 'item', '000000000000000000000000000000000000000000000000000000000000000000', 4, 1, 'Параметры бонусного итема за SMS (50) голосов MMOTOP'),
('mmotop4_item', 'item', '000000000000000000000000000000000000000000000000000000000000000000', 4, 1, 'Параметры бонусного итема за SMS (100) голосов MMOTOP'),
('', 'desc', 'Настройка бонусов за голосование в Q-TOP', 4, 2, ''),
('qtop_statlink', 'string', '', 4, 2, 'Ссылка на статистику голосов Q-TOP'),
('qtop_cost1', 'cost', '3|0', 4, 2, 'Стоимость обычного голоса'),
('qtop_cost2', 'cost', '5|0', 4, 2, 'Стоимость SMS голоса'),
('send_qtop_message', 'bool', '1', 4, 2, 'Отправлять сообщение в ГМ чат после выдачи бонусов'),
('qtop_message', 'string', '%d игроков только что получили бонусы за голосование в Q-Top. Поддержи проект - получи бонус!', 4, 2, 'Сообщение в ГМ чат после выдачи бонусов'),
('send_qtop_bonusitem', 'bool', '0', 4, 2, 'Отправлять предмет за голосование'),
('qtop1_item', 'item', '000000000000000000000000000000000000000000000000000000000000000000', 4, 2, 'Параметры бонусного итема за обычный голос Q-TOP'),
('qtop2_item', 'item', '000000000000000000000000000000000000000000000000000000000000000000', 4, 2, 'Параметры бонусного итема за SMS голос Q-TOP'),
('', 'desc', 'Параметры хранения логов голосования', 4, 3, ''),
('top_log_lifetime', 'int', '0', 4, 3, 'Срок хранения логов выдачи голосования в месяцах (0 - не чистить лог)'),
('admin_email', 'string', 'admin@myserv.com', 5, 0, 'Почта, указываемая для ответа при отправки письма при смене пароля и т.п.'),
('mail_type', 'select', 'PhpMail,SMTP,1', 5, 0, 'Способ отправки почты'),
('', 'desc', 'Настройки SMTP', 5, 1, 'Актуально только при выбранном SMTP способе отправки почты'),
('smtp_host', 'string', 'smtp.gmail.com', 5, 1, 'Cервер для отправки почты'),
('smtp_port', 'int', '587', 5, 1, 'Порт для отправки почты'),
('smtp_username', 'string', 'admin@myserv.com', 5, 1, 'Имя пользователя на SMTP сервере'),
('smtp_password', 'string', '', 5, 1, 'Пароль пользователя на SMTP сервере'),
('smtp_from', 'string', 'Администрация', 5, 1, 'Ваше имя. Будет показывать при прочтении в поле \"От кого\"'),
('smtp_charset', 'string', 'UTF-8', 5, 1, 'Кодировка сообщений. Без надобности не трогать'),
('smtp_debug', 'select', '0,1,2,3,4,0', 5, 1, 'Вывод отладочной информации при отправке почты\r\n	0 - Без вывода отладочной информации\r\n	1 - Вывод комманд\r\n	2 - Вывод данных и комманд\r\n	3 - Вывод данных, комманд и статусов соединений\r\n	4 - Low-level data output'),
('smtp_logfile', 'string', 'logs/mail_log_1.log', 5, 1, 'Название файла логов для записи отладочной информации при отправке писем'),
('smtp_secure', 'select', '0,ssl,tls,2', 5, 1, 'Префикс безопасного соединения, <b>ssl</b>, <b>tls</b> или пустой.'),
('smtp_timeout', 'int', '60', 5, 1, 'Таймаут запроса в секундах'),
('', 'desc', 'Шаблоны писем', 5, 2, ''),
('reg_template', 'text', '<h3>Данные, указанные при регистрации аккаунта:</h3>\r\n<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\" align=\"center\">\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Логин:</font></td>\r\n    <td align=\"left\" valign=\"top\"><font color=\"#0000ff\">{LOGIN}</font></td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Пароль:</font></td>\r\n    <td align=\"left\" valign=\"top\">{PASSW}</td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Вопрос:</font></td>\r\n    <td align=\"left\" valign=\"top\">{QUESTION}</td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Ответ:</font></td>\r\n    <td align=\"left\" valign=\"top\">{ANSWER}</td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Имя:</font></td>\r\n    <td align=\"left\" valign=\"top\">{NAME}</td>\r\n</tr>\r\n</table>\r\n<br>\r\n{ACT_TEMPLATE}\r\n<p>Приглашайте друзей регистрироваться на проекте по реферальной ссылке и получайте бонусы!</p>\r\n<p>Ваша реферальная ссылка: <a href=\"{REF_LINK}\" target=\"_blank\">{REF_LINK}</a></p><br>\r\n<p>Просьба учесть, что все эти данные будут нужны для смены пароля. Приятной игры!</p><br>\r\n<p><i>Отвечать на это письмо не нужно, оно было сгенерировано автоматически, а робот не любит читать письма ;).</i></p>', 5, 2, 'Письмо после регистрации аккаунта в HTML формате\r\n	{LOGIN} - логин аккаунта\r\n	{PASSW} - пароль аккаунта\r\n	{QUESTION} - Секретный вопрос\r\n	{ANSWER} - ответ на вопрос\r\n	{NAME} - имя\r\n	{ACT_TEMPLATE} - шаблон активации аккаунта\r\n	{REF_LINK} - реферальная ссылка'),
('act_template', 'text', '<p>Чтобы активировать аккаунт, перейдите по следующей ссылке: <a href="{ACT_LINK}">{ACT_LINK}</a></p>', 5, 2, 'Текст активации аккаунта в HTML формате\r\n	{ACT_LINK} - ссылка для активации аккаунта'),
('reset_template', 'text', '<p>С айпи адреса {IP} был получен запрос на сброс пароля от аккаунта <b>{LOGIN}</b> сервера <b>{SERVNAME}</b>.<br>\r\nПароль был успешно изменен.</p><br>\r\n<h3>Данные вашего аккаунта:</h3>\r\n<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\" align=\"center\">\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Логин:</font></td>\r\n    <td align=\"left\" valign=\"top\"><font color=\"#0000ff\">{LOGIN}</font></td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Новый пароль:</font></td>\r\n    <td align=\"left\" valign=\"top\"><font color=\"#a00000\">{PASSW}</font></td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Вопрос:</font></td>\r\n    <td align=\"left\" valign=\"top\">{QUESTION}</td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Ответ:</font></td>\r\n    <td align=\"left\" valign=\"top\">{ANSWER}</td>\r\n</tr>\r\n</table>', 5, 2, 'Письмо после сброса пароля аккаунта в HTML формате\\r\\n\r\n	{IP} - IP адрес с которого был сделан запрос\\r\\n\r\n	{LOGIN} - логин аккаунта\\r\\n\r\n	{PASSW} - новый пароль аккаунта\\r\\n\r\n	{QUESTION} - Секретный вопрос\\r\\n\r\n	{ANSWER} - ответ на вопрос\r\n	{SERVNAME} - Название сервера'),
('change_passw_template', 'text', '<p>С айпи адреса {IP} был получен запрос на смену пароля от аккаунта <b>{LOGIN}</b> сервера <b>{SERVNAME}</b>.<br>\r\nПароль был успешно изменен.</p><br>\r\n<h3>Данные вашего аккаунта:</h3>\r\n<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\" align=\"center\">\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Логин:</font></td>\r\n    <td align=\"left\" valign=\"top\"><font color=\"#0000ff\">{LOGIN}</font></td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Новый пароль:</font></td>\r\n    <td align=\"left\" valign=\"top\"><font color=\"#a00000\">{PASSW}</font></td>\r\n</tr>\r\n</table>', 5, 2, 'Письмо после смены пароля аккаунта в HTML формате\\r\\n\r\n	{IP} - IP адрес с которого был сделан запрос\\r\\n\r\n	{LOGIN} - логин аккаунта\\r\\n\r\n	{PASSW} - новый пароль аккаунта\\r\\n\r\n	{SERVNAME} - Название сервера'),
('rem_ip_process_template', 'text', '<p>С айпи адреса {IP} был получен запрос на отключение ограничения входа с определенных IP на аккаунт <b>{LOGIN}</b> сервера <b>{SERVNAME}</b>.<br>\r\nОграничение было успешно отключено.</p>', 5, 2, 'Письмо после сброса привязки аккаунта по IP в HTML формате\r\n	{IP} - IP адрес, с которого была заявка\\r\\n\r\n	{LOGIN} - логин аккаунта\\r\\n\r\n	{SERVNAME} - Название сервера'),
('rem_template', 'text', '<p>С айпи адреса {IP} был получен запрос на восстановление данных от аккаунта по вашему E-mail на сервере <b>{SERVNAME}</b>.<br>\r\nЕсли Вы не запрашивали данную процедуру, просто проигнорируйте данное сообщение.</p><br>\r\n<h3>Данные ваших аккаунтов:</h3>\r\n{ACCOUNTS_DATA}', 5, 2, 'Письмо восстановления данных аккаунтов в HTML формате\r\n	{IP} - IP адрес, с которого была заявка\r\n	{SERVNAME} - Название сервера\r\n	{ACCOUNTS_DATA} - данные аккаунтов с ссылками для восстановления доступа'),
('acc_data_template', 'text', '<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\" align=\"center\">\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Логин:</font></td>\r\n    <td align=\"left\" valign=\"top\"><font color=\"#0000ff\">{LOGIN}</font></td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Вопрос:</font></td>\r\n    <td align=\"left\" valign=\"top\">{QUESTION}</td>\r\n</tr>\r\n<tr>\r\n    <td width=\"150px\" align=\"right\" valign=\"top\"><font color=\"#aa0000\">Ответ:</font></td>\r\n    <td align=\"left\" valign=\"top\">{ANSWER}</td>\r\n</tr>\r\n</table>\r\n<p>Для сброса пароля на этом аккаунте, перейдите по этой ссылке <a href=\"{RESET_PASSW_LINK}\">{RESET_PASSW_LINK}</a></p>\r\n<p>Для отключения ограничения входа с определенных IP/подсетей, перейдите по этой ссылке <a href=\"{RESET_IP_LINK}\">{RESET_IP_LINK}</a></p>\r\n<br>', 5, 2, 'Текст данных аккаунта в HTML формате\r\n	{LOGIN} - логин аккаунта\r\n	{QUESTION} - Секретный вопрос\r\n	{ANSWER} - ответ на вопрос\r\n	{RESET_PASSW_LINK} - ссылка для сброса пароля на аккаунте\r\n	{RESET_IP_LINK} - ссылка для отключения запрета входа по IP на аккаунте'),
('test_mail_template', 'text', '<p>Если вы читаете данное письмо, значит настройки сделаны верно</p>', 5, 2, 'Текст тестового письма в HTML формате'),
('kaptcha_type', 'select', 'Internal,ReCaptcha v2,0', 6, 0, 'Тип капчи'),
('', 'desc', 'Параметры ReCaptcha v2', 6, 1, 'Получить данные <a href="https://www.google.com/recaptcha/admin#list" target="blank">можно тут</a>'),
('recaptcha2_site_key', 'string', '', 6, 1, 'Публичный ключ'),
('recaptcha2_secret_key', 'string', '', 6, 1, 'Секретный приватный ключ');
UPDATE `config` SET `group`=2 WHERE `name`='freekassa_merchant_id' OR `name`='freekassa_secret_word1' OR `name`='freekassa_secret_word2' OR `value`='Параметры сервиса Free-Kassa';
INSERT INTO `config` (`name`, `type`, `value`, `section`, `group`, `desc`) VALUES
('freekassa_new_version', 'bool', '0', 3, 2, 'Использовать ссылку для новой версии сайта freekassa.ru\r\nВыключить, если используется старый сайт free-kassa.ru');