	<div id="content">
		<?= form_open('user/login') ?>
		<div>
			<?= form_label('Email Address', 'email') ?>
			<?= form_input(array('name'=>'email', 'id'=>'email', 'value'=>set_value('email'))) ?>
		</div>
		<div>
			<?= form_label('Password', 'password') ?>
			<?= form_input(array('name'=>'password', 'id'=>'password', 'value'=>'')) ?>
		</div>
		<?= form_submit('submit', 'Login') ?>
	</div>
</body>
</html>