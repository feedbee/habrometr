<?php echo $this->menuView(null, '/register'); ?>

<h1>Получите свой Хаброметр</h1>
<?php if ($this->ok) { ?>
<div class="alert alert-success">Регистрация пройдена. <a href="./users/<?php print $this->userCode; ?>/">Ваша страница</a>.
	<a href="./users/<?php print $this->userCode; ?>/get/">Код ваших Хаброметров</a>.
</div>
<?php } else { ?>
<p>Введите ваш хабрологин в форму ниже, чтобы получить свой Хаброметр. E-mail вводить не обязательно, но если вы его введете,
	то он будет использоваться исключительно для уведомлений о работе системы.</p>
<?php if ($this->errors) { ?>
<div class="alert alert-error"><ul>
	<?php $e = $this->errors; 
		  foreach ($e as $error) { ?><li><?php echo $error; ?></li><?php } ?>
</ul></div>
<?php } ?>
<form action="./register/">
	<legend>Регистрация</legend>
	<label for="user_code">Хабралогин:</label>
	<input type="text" id="user_code" name="user_code" size="25" <?php if($this->userCode !== '')
		print 'value="' . htmlspecialchars($this->userCode) . '"'; ?>/> 
	<span class="help-inline"><small>Поле обязательно для заполнения</small></span>
	<label for="user_email">E-mail:</label>
	<input type="text" id="user_email" name="user_email" size="25" <?php if($this->userEmail !== '')
		print 'value="' . htmlspecialchars($this->userEmail) . '"'; ?>/>
	<span class="help-inline"><small>Можно не заполнять</small></span>
	<div class="form-actions">
		<input class="btn btn-primary" type="submit" value="Отправить">
		<input class="btn btn" type="reset" value="Очистить">
	</div>
</form>
<?php } ?>