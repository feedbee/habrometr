<?php

$buildPath = $this->userListPathBuilder;

echo $this->menuView(null, '/users');
?>
<h1>Пользователи Хаброметра</h1>
<?php if (!is_null($this->filter)) { ?>
<p>Список пользователей Хабрахабра по запросу "<?php echo htmlspecialchars($this->filter); ?>".<?php
	if ($this->usersOveral > 0) {?> Найдено <?php echo $this->usersOveral; ?> человек<?php
		echo $this->flection($this->usersOveral, array('', 'а', '')); ?>.<?php } else { ?> Ничего не найдено<? } ?></p>
<?php } else { ?>
<p>Список пользователей Хабрахабра, которые зарегистрировались в Хаброметре.<?php
 if ($this->usersOveral > 0) {?> На сегодня это <?php echo $this->usersOveral; ?> человек<?php 
 	echo $this->flection($this->usersOveral, array('', 'а', '')); ?>.<?php } ?></p>
<?php } ?>

<p>
	Порядок вывода: <?php echo $this->selectorView(array(
										'./users/' . $buildPath(null, null, $this->filter) => 'по времени регистрации',
										'./users/' . $buildPath('name', null, $this->filter) => 'по алфавиту'
									), './users/' . $buildPath($this->requestedOrder, null, $this->filter)); ?>
</p>

<form action="./users/<?php echo $buildPath($this->requestedOrder); ?>">
	<p><label for="form_filter">Поиск: <input id="form_filter" name="filter" type="text" class="search-query" placeholder="Хабралогин"
		<?php if (!is_null($this->filter)) { ?> value="<?php echo htmlspecialchars($this->filter); ?>"<?php } ?>></label></p>
</form>

<?php
$l = $this->userList;
if ($this->usersOveral < 1) { ?><p class="muted">Ни один пользователь <?php if (!is_null($this->filter)) { ?>не найден<? } else { ?>пока не зарегистрировался.<? } ?></p><?php } else { ?>
<ul class="unstyled">
<?php
		foreach($l as $user)
		{
			?>	<li><i class="icon-user"></i> <a href="./users/<?php echo $user['user_code']; ?>/"><?php echo $user['user_code']; ?></a></li>
<?
		}
?></ul>

<div class="pagination">
	<ul>
<?php if ($this->page > 1) { ?>
		<li>
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $this->page - 1, $this->filter); ?>">«</a>
		</li>
<?php } else { ?>
		<li class="disabled">
			<span>«</span>
		</li>
<?php } ?>
		
<?php

	$overalPages = $this->overalPages;
	$fixMin = function($value) { return $value < 1 ? 1: $value; };
	$fixMax = function($value) use ($overalPages) { return $value > $overalPages ? $overalPages : $value; };
	
	$oneSideInterval = 5;
	$leftPoint = $this->page - $oneSideInterval;
	$rightPoint = $this->page + $oneSideInterval;
	$startPage = $fixMin($leftPoint);
	$finishPage = $fixMax($rightPoint);
	if ($finishPage != $rightPoint)
	{
		$leftPoint -= $rightPoint - $finishPage;
		$startPage = $fixMin($leftPoint);
	}
	if ($startPage != $leftPoint)
	{
		$rightPoint += (2 - $leftPoint) - $startPage;
		$finishPage = $fixMax($rightPoint);
	}

	if ($finishPage != $overalPages) {
		$finishPage -= 1;
	}
	
	if ($startPage != 1) { $startPage += 1; ?>
		<li>
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $fixMin($this->page - $oneSideInterval * 2), $this->filter); ?>">...</a>
		</li>
<?php
	}

	for ($i = $startPage; $i <= $finishPage; $i++) { ?>
		<li<?php if ($i == $this->page) { ?> class="active"<?php } ?>>
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $i, $this->filter); ?>"><?php echo $i; ?></a>
		</li>
<?php } ?>

<?php if ($finishPage != $overalPages) { ?>
		<li>
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $fixMax($this->page + $oneSideInterval * 2), $this->filter); ?>">...</a>
		</li>
<?php }

		if ($this->page < $overalPages) { ?>
		<li>
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $this->page + 1, $this->filter); ?>">»</a>
		</li>
<?php } else { ?>
		<li class="disabled">
			<span>»</span>
		</li>
<?php } ?>
	</ul>
</div>

<?php } ?>