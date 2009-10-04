<?php echo $this->menuView(); ?>
<h1>Код Хаброметров пользователя <a href="./users/<?php print $this->userCode; ?>/"><?php print $this->userCode; ?></a></h1>
<p>Внимание. Код информера изменять <strong>разрешается</strong>. Например, если вы хотите убрать ссылку на Хаброграф, чтобы информер лаконично вписался в ваш сайт, вы <strong>можете</strong> это сделать.</p>
<?php $s = $this->sizes; foreach($s as $size) { ?>
<h2><?php print "{$size['x']}x{$size['y']}"; ?></h2>
<p><img src="./habrometr_<?php print "{$size['x']}x{$size['y']}"; ?>_<?php print $this->userCode; ?>.png" width="<?php print $size['x']; ?>" height="<?php print $size['y']; ?>" alt="Хаброметр <?php print $this->userCode; ?>" title="Хаброметр <?php print $this->userCode; ?>" /></p>
<textarea cols="60" rows="6">&lt;a href=&quot;<?php print Config::SERVICE_URL; ?>/"&gt;Хаброметр&lt;/a&gt;&lt;br /&gt;&lt;a href="<?php print Config::SERVICE_URL; ?>/users/<?php print $this->userCode; ?>/"&gt;&lt;img src="<?php print Config::SERVICE_URL; ?>/habrometr_<?php print "{$size['x']}x{$size['y']}"; ?>_<?php print $this->userCode; ?>.png" width="<?php print $size['x']; ?>" height="<?php print $size['y']; ?>" alt="Хаброметр <?php print $this->userCode; ?>" title="Хаброметр <?php print $this->userCode; ?>" /&gt;&lt;/a&gt;</textarea>
<?php } 