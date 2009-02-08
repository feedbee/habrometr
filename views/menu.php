<?php
if (is_array($this->elements))
	$elements = $this->elements;
else
	$elements = array();
?><p><?php foreach($elements as $key => $value) { if ($key == $this->active) {?><strong><?php echo $value['text']; ?></strong> <?php } else { ?><a href="<?php echo $value['url']; ?>"<?php if(isset($value['external'])&&$value['external']) echo ' target="blank"'; ?>><?php echo $value['text']; ?></a> <?php }} ?></p>
