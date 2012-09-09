<?php
if (is_array($this->elements))
	$elements = $this->elements;
else
	$elements = array();
?>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="./">Хаброметр</a>
			<p class="navbar-text pull-right">
			Друзья: <a href="http://www.bankinform.ru/HabraEditor/">ХабраРедактор</a>
		    </p>
			<ul class="nav">
				<?php foreach($elements as $key => $value) {
					  if ($key == $this->active) {?>
				<li class="active">
					<a href="<?php echo $value['url']; ?>"<?php if(isset($value['external'])&&$value['external']) echo ' target="blank"'; ?>><?php echo $value['text']; ?></a></li>
				<?php } else { ?>
				<li><a href="<?php echo $value['url']; ?>"<?php if(isset($value['external'])&&$value['external']) echo ' target="blank"'; ?>><?php echo $value['text']; ?></a></li>
				<?php }} ?>
			</ul>
		</div>
	</div>
</div>