	<div>
		<table>
		<thead>
			<tr>
<?php
foreach ($fields as $name=>$field) {
	if (strpos($field->access, 'L') !== false) { ?>
				<th><?= $field->label ?></th>
<?php	}
} ?>
				<th>View</th>
			</tr>
		</thead>
		<tbody>
<?php
//echo "<pre>item: ".print_r($item, true)."</pre>\n";
//echo "<pre>fields: ".print_r($fields, true)."</pre>\n";
foreach($rows as $item) { echo "<pre>item: ".print_r($item, true)."</pre>\n"; ?>
			<tr>
<?php	foreach ($fields as $name=>$field) {
		if (strpos($field->access, 'L') !== false) {
//			$value = $item->$name;
			if($field->type == 'datetime') {
				if(isset($field->format)) $format = $field->format;
				else $format = 'g:i a M j, Y';
				$value = empty($item->$name) ? '' : date($format, strtotime($item->$name));
			} elseif($field->type == 'date') {
				if(isset($field->format)) $format = $field->format;
				else $format = 'M j, Y';
				$value = empty($item->$name) ? '' : date($format, strtotime($item->$name));
			} elseif($field->type == 'button') {
				$value = "<button type=\"button\" onclick=\"window.location = '$field->location'\">$field->text</button>";
			} else {
				$value = $item->$name;
			}
?>
				<td><?= $value ?></td>
<?php		}
	} ?>
				<td><button type="button" onclick="window.location = '<?= site_url('race/view/'.$item->id) ?>'">View</button></td>
			</tr>
<?php } ?>
		</tbody>
		</table>
	</div>
