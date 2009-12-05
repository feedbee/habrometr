<?php echo $this->menuView(null, '/users'); ?>
<h1>Список всех пользователей Хаброметра</h1>
<p>Все пользователи перечислены в порядке регистрации.</p>
<ul>
<?php
$l = $this->userList;
if (!count($l))
{
	print "Ни один пользователь пока не зарегистрировался.";
}
else
{
		foreach($l as $user)
		{
			print "<li><a href=\"./users/{$user['user_code']}/\">{$user['user_code']}</a></li>";
		}
}
?>
</ul>
