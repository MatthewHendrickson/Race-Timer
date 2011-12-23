<?php if ($method == 'view' || $method == 'add') { ?>
	<div id="footer">
		<button onclick="window.location = '<?= site_url('racer/rows/'.$idrace) ?>'">List all racers</button>
	</div>
<?php } else if ($method == 'rows') { ?>
	<div id="footer">
		<button onclick="window.location = '<?= site_url('racer/add/'.$idrace) ?>'">Add new racer</button>
	</div>
<?php } ?>
</body>
</html>
