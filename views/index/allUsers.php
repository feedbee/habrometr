<?php echo $this->menuView(null, '/users'); ?>
<h1>Список всех пользователей Хаброметра</h1>
<p>Все пользователи перечислены в порядке регистрации.</p>
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
		<li><a href="./users/<?php echo $this->order; ?>page-<?php echo $this->page- 1; ?>/">«</a></li>
<?php } else { ?>
		<li class="disabled"><span>«</span></li>
<?php } ?>
		
<?php for ($i = 1; $i <= $this->overalPages; $i++) { ?>
		<li<?php if ($i == $this->page) { ?> class="active"<?php } ?>><a href="./users/<?php echo $this->order; ?>page-<?php echo $i; ?>/"><?php echo $i; ?></a></li>
<?php } ?>

<?php if ($this->page < $this->overalPages) { ?>
		<li><a href="./users/<?php echo $this->orderMark; ?>page-<?php echo $this->page + 1; ?>/">»</a></li>
<?php } else { ?>
		<li class="disabled"><span>»</span></li>
<?php } ?>
	</ul>
</div>

<?php } ?>