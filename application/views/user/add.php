	<div id="content">
		<?= form_open('user/add')."\n" ?>
		<div>
			<?= form_label('User Name', 'username')."\n" ?>
			<?= form_input(array('name'=>'username', 'id'=>'username', 'value'=>set_value('username')))."\n" ?>
		</div>
		<div>
			<?= form_label('Email Address', 'email')."\n" ?>
			<?= form_input(array('name'=>'email', 'id'=>'email', 'value'=>set_value('email')))."\n" ?>
		</div>
		<div>
			<?= form_label('Password', 'password')."\n" ?>
			<?= form_password(array('name'=>'password', 'id'=>'password', 'value'=>''))."\n" ?>
		</div>
		<div>
			<?= form_label('Repeat Password', 'password2')."\n" ?>
			<?= form_password(array('name'=>'password2', 'id'=>'password2', 'value'=>''))."\n" ?>
		</div>
		<?= form_submit('submit', 'Create Account')."\n" ?>
	</div>
</body>
</html>