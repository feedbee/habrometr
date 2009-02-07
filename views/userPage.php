<?php echo $this->menuView(); ?>
<h1>Страница хабраюзера <a href="http://<?php print $this->userData['user_code']; ?>.habrahabr.ru/"><?php print $this->userData['user_code']; ?></a></h1>
<?php if (!$this->current) { ?>
<p>Данные по пользователю пока не запрашивались.</p>
<?php } else { ?>
<h2>Текущие хабразначения:</h2>
<ul>
	<li>Карма: <?php print $this->current['karma_value']; ?></li>
	<li>Хабрасила: <?php print $this->current['habraforce']; ?></li>
	<li>Позиция в рейтинге: <?php print $this->current['rate_position']; ?></li>
</ul>
<?php } ?>

<p><a href="./users/<?php print $this->userData['user_code']; ?>/get/">Коды Хаброметров пользователя</a></p>

<h2>История хабразначений (последние 500 записей)</h2>
<?php if (!$this->history) { ?>
<p>История пуста.</p>
<?php } else { ?>
<table border="1" cellpadding="3" cellspacing="0">
	<tr>
		<th>Карма</th>
		<th>Хабрасила</th>
		<th>Рейтинг</th>
		<th>Дата</th>
	</tr>
	<?php $h = $this->history; foreach($h as $row) { 
		print "\t<tr>\r\n\t\t<td>{$row['karma_value']}</td>\r\n\t\t<td>{$row['habraforce']}</td>\r\n\t\t<td>{$row['rate_position']}</td>\r\n\t\t<td>{$row['log_time']}</td>\r\n\t<tr>\r\n";
	} ?>
</table>
<?php } ?>