<div class="users form">
	<h3>Login</h3>
	<?= $this->Form->create() ?>
	<fieldset>
		<legend><?= __('Please enter name and password') ?></legend>
		<?php
			echo $this->Form->control('name');
			echo $this->Form->control('password');
		?>
	</fieldset>
	<?= $this->Form->submit(__('Submit')) ?>
	<?= $this->Form->end() ?>
</div>
