<?php if ($method == 'view' || $method == 'add') { ?>
	<div id="footer">
		<button onclick="window.location = '<?= site_url('race/rows') ?>'">List all races</button>
	</div>
<?php } else if ($method == 'rows') { ?>
	<div id="footer">
		<button onclick="window.location = '<?= site_url('race/add') ?>'">Add new race</button>
	</div>
<?php } ?>
</body>
</html>
