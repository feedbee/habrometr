<?php echo $this->menuView(null, '/users'); ?>
<h1>Список всех пользователей Хаброметра</h1>
<ul>
<?php
$l = $this->userList;
foreach($l as $user)
{
	print "<li><a href=\"./users/{$user['user_code']}/\">{$user['user_code']}</a></li>";
}
?>
</ul>