<?php
if (is_array($this->elements))
	$elements = $this->elements;
else
	$elements = array();
?>
<div>
	<?php foreach($elements as $key => $value) {
		if ($key == $this->active) {?>
	<strong><?php echo $value['text']; ?></strong>&nbsp;&nbsp; 
	<?php } else { ?>
	<a href="<?php echo $value['url']; ?>"<?php if(isset($value['external'])&&$value['external']) echo ' target="blank"'; ?>><?php echo $value['text']; ?></a>&nbsp;&nbsp; 
	<?php }} ?> 
	<div style="float:right">
		Друзья: <a href="http://www.bankinform.ru/HabraEditor/">ХабраРедактор</a>,
		<a href="http://habradigest.ru/">habradigest</a>, <a href="http://progg.ru/">progg.ru</a>
	</div>
</div>