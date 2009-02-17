<?php echo $this->menuView(null, '/'); ?>
<h1><img src="./stuff/logo.png" width="185" height="130" alt="Хаброметр (Habrometr)" title="Хаброметр (Habrometr)" align="left" />Хаброметр (Habrometr) — сбор и графическое отображение показателей хабраюзеров</h1>
<address>Версия Habrometr <?php print $this->version; ?> beta 4 (testing)</address>
<p>Хаброметр — новая система сбора мониторинга кармы и хабрасилы. Пока находится на стадии разработки. Автор — <a href="http://valera.ws/">Валера Леонтьев</a> (<a href="http://feedbee.habrahabr.ru/">feedbee</a>).</p>
<p>Связь с автором по e-mail или jabber: <a href="mailto:feedbee@gmail.com">feedbee@gmail.com</a>.</p>
<h2>Сбор информации</h2>
<p>Сбор хабрапоказателей ведется системой через <a href="http://habrahabr.ru/api/profile/feedbee/">API Хабрахабра</a> в соответствии с <a href="http://habrahabr.ru/info/help/bots/">Правилами</a>, установленными Администрацией ресурса.</p>
<p>Запрос показателей по списку пользователей (на данный момент только один пользователь — feedbee) проводится раз в 2 часа (начиная от 0:00) с IP=93.174.6.118 (server.valera.ws). Запрос отправляется сервер habrahabr.ru по адресу вида http://www.habrahabr.ru/api/profile/%username%/. Каждый профиль запрашивается не чаще, чем раз в 2 часа. В случае ошибки повторный запрос на сервер не проводится.</p>
<p>В заголовке User-Agent HTTP-запроса передается следующая информация:</p><pre>sprintf("PHP/%s (Habrometr/%s; feedbee@gmail.com; http://habrometr.ru/)", PHP_VERSION, self::VERSION),</pre><p>где PHP_VERSION — константа, устанавливаемая PHP Engine, self::VERSION — идентификатор версии Хаброметра, включающий номер версии и номер подверсии (например, 0.2).</p>
<h2>Регистрация</h2>
<p><a href="./register/">Зарегистрироваться</a> для получения Хаброметра может каждый желающий Хабраюзер. Но учтите, что пока сервис находится на стадии глубокого бета-тестирования.</p>
<h2>Новости</h2>
<p><strong>16 февраля</strong> поправил еще пару ошибок в коде и <a href="http://feedbee.habrahabr.ru/blog/51978/">рассказал</a> Хабралюдям про сервис.</p>
<p><strong>8 февраля</strong> добавлены новые размеры Хаброметров: 88х31, 31х31 и 350х20. Кроме того, изменена подача истории на странице пользователя.</p>
<p><strong>7 февраля</strong> очередная версия залита на сервер. Вместе с этим публикуется исходный код Хаброметра. Скрипты распространяются под лицензией GPL3, по этому теперь любой желающий может разместить свой Хаброметр на своем сервере, а так же публично предоставлять сервис для других пользователей.</p>
<p><strong>31 января</strong> доработан код, исправлена пара мелких ошибок. Тестируем версию 0.5. Следующие шаги — окончательная дороботка и оформление кода, небольшая переработка страницы пользователя, добавление информеров других размеров, опубликование исходников.</p>
<p><strong>25 января</strong> наконец-то доведены до конца работы по серверу, установлено и настроено все необходимое ПО, а так же свершен официальный переезд на домен habrometr.ru. + Сдано 2 экзамена в универе. Выходные прошли успешно :) Сейчас сервер работает на nginx (фронт-энд), apache (бэкэнд), PHP (+Curl, +IMagick, +eaccelerator), MySQL, memcached и кэшируется все, что можно закэшировать :)</p>
<p><strong>24 января</strong> на сервере установлен фронт-энд прокси nginx, который поможет справиться с нагрузкой, когда про Хаброметр узнает все Хабрасообщество. Если в связи с этим появились какие-то баги, огромная просьба отписывать на <a href="mailto:feedbee@gmail.com">e-mail</a>.</p>
<p>Сервис потихоньку развивает в соответствии с наличием времени у автора. <strong>23 января</strong> на сервер выложена новая версия кода, значительно доработанная и доведенная до ума. Уже близок час открытия кода под лицензией GPL.</p>
<p>Уже больше недели Хаброметры успешно работают на страницах десятков пользователей. Первый уровень тестирования пройден и все работает относительно стабильно.</p>
<img src="./habrometr_88x120_feedbee.png" width="88" height="120" alt="Хаброметр feedbee" title="Хаброметр feedbee" /> <img src="./habrometr_425x120_feedbee.png" width="425" height="120" alt="Хаброметр feedbee" title="Хаброметр feedbee" /> <img src="./habrometr_88x31_feedbee.png" width="88" height="31" alt="Хаброметр feedbee" title="Хаброметр feedbee" /> <br /><br />
<img src="./habrometr_88x15_feedbee.png" width="88" height="15" alt="Хаброметр feedbee" title="Хаброметр feedbee" /> <img src="./habrometr_350x20_feedbee.png" width="350" height="20" alt="Хаброметр feedbee" title="Хаброметр feedbee" />
