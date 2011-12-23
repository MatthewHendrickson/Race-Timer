	<div id="content">
<?php
//echo "<pre>item: ".print_r($item, true)."</pre>\n";
//echo "<pre>fields: ".print_r($fields, true)."</pre>\n";
echo form_open($cName.'/edit/'.$item->id)."\n";
foreach ($fields as $name=>$field) {
	if (strpos($field->access, 'E') !== false) {
		echo "<div>\n";
		echo form_label($field->label, $name);
		if($field->type == 'varchar' || $field->type == 'int' || $field->type == 'datetime' || $field->type == 'date') {
			echo form_input(array('name'=>$name, 'id'=>$name, 'value'=>$item->$name))."\n";
		}
		echo "</div>\n";
	}
}
echo form_submit('submit', 'Save');
echo form_close()."\n";
?>
	</div>
<?php if (!empty($id)) { ?>
	<div>
		<button onclick="window.location = '<?= site_url($cName.'/view/'.$id) ?>'">Cancel</button>
	</div>
<?php } ?>
