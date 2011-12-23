	<div>
<?php
//echo "<pre>item: ".print_r($item, true)."</pre>\n";
//echo "<pre>fields: ".print_r($fields, true)."</pre>\n";
foreach ($fields as $name=>$field) {
	if (strpos($field->access, 'V') !== false) {
		$value = $item->$name;
		if($field->type == 'datetime') {
			if(isset($field->format)) $format = $field->format;
			else $format = 'g:i a M j, Y';
			$value = empty($value) ? '' : date($format, strtotime($value));
		} elseif($field->type == 'date') {
			if(isset($field->format)) $format = $field->format;
			else $format = 'M j, Y';
			$value = empty($value) ? '' : date($format, strtotime($value));
		}
?>
		<div>
			<span><?= $field->label ?>: </span>
			<span><?= $value ?></span>
		</div>
<?php	}
}
?>
	</div>
	<div>
		<button onclick="window.location = '<?= site_url($cName.'/edit/'.$id) ?>'">Edit</button>
	</div>
