<?php

$buildPath = $this->userListPathBuilder;

echo $this->menuView(null, '/users');
?>
<h1>Пользователи Хаброметра</h1>
<p>Список пользователей Хабрахабра, которые зарегистрировались в Хаброметре.</p>

<p>
	Порядок вывода: <?php echo $this->selectorView(array(
										'./users/' . $buildPath() => 'по времени регистрации',
										'./users/' . $buildPath('name') => 'по алфавиту'
									), './users/' . $buildPath($this->requestedOrder)); ?>
</p>

<?php
$l = $this->userList;
if (!count($l)) { ?><p class="muted">Ни один пользователь пока не зарегистрировался.</p><?php } else { ?>
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
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $this->page - 1); ?>">«</a>
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
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $fixMin($this->page - $oneSideInterval * 2)); ?>">...</a>
		</li>
<?php
	}

	for ($i = $startPage; $i <= $finishPage; $i++) { ?>
		<li<?php if ($i == $this->page) { ?> class="active"<?php } ?>>
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $i); ?>"><?php echo $i; ?></a>
		</li>
<?php } ?>

<?php if ($finishPage != $overalPages) { ?>
		<li>
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $fixMax($this->page + $oneSideInterval * 2)); ?>">...</a>
		</li>
<?php }

		if ($this->page < $overalPages) { ?>
		<li>
			<a href="./users/<?php echo $buildPath($this->requestedOrder, $this->page + 1); ?>">»</a>
		</li>
<?php } else { ?>
		<li class="disabled">
			<span>»</span>
		</li>
<?php } ?>
	</ul>
</div>

<?php } ?>