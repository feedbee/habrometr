<?php
if (is_array($this->elements))
	$elements = $this->elements;
else
	$elements = array();

foreach($elements as $key => $value) {
	  if ($key == $this->active) {?>
<span class="label label-info">
	<?php echo $value; ?>
</span>
<?php } else { ?>
<a href="<?php echo $key; ?>" class="label label-transparent" ><?php echo $value; ?></a>
<?php }} ?>