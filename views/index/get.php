<?php echo $this->menuView(); ?>
<h1>Код Хаброметров пользователя <a href="./users/<?php print $this->userCode; ?>/"><?php print $this->userCode; ?></a></h1>
<div class="alert alert-info">
	<small>Код информера <strong>разрешается</strong> изменять. Например, если вы хотите убрать ссылку на Хаброметр,
	чтобы информер лаконично вписался в дизайн вашего сайта, сделайте это.</small>
</div>

<?php $s = $this->sizes; foreach($s as $size) { ?>
<h3><?php print "{$size['x']}x{$size['y']}"; ?></h3>
<p>
	<img src="./habrometr_<?php print "{$size['x']}x{$size['y']}"; ?>_<?php print $this->userCode; ?>.png" width="<?php print $size['x']; ?>"
		height="<?php print $size['y']; ?>" alt="Хаброметр <?php print $this->userCode; ?>" title="Хаброметр <?php print $this->userCode; ?>" />
</p>
<textarea class="span8" rows="3">&lt;a href=&quot;<?php print Config::SERVICE_URL; ?>/"&gt;Хаброметр&lt;/a&gt;&lt;br /&gt;&lt;a href="<?php print Config::SERVICE_URL; ?>/users/<?php print $this->userCode; ?>/"&gt;&lt;img src="<?php print Config::SERVICE_URL; ?>/habrometr_<?php print "{$size['x']}x{$size['y']}"; ?>_<?php print $this->userCode; ?>.png" width="<?php print $size['x']; ?>" height="<?php print $size['y']; ?>" alt="Хаброметр <?php print $this->userCode; ?>" title="Хаброметр <?php print $this->userCode; ?>" /&gt;&lt;/a&gt;</textarea>
<?php } 