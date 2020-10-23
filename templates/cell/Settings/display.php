<div id='settings'>
<?php 
	if ($this->Identity->isLoggedIn()) {
		echo $this->Identity->get('name'), '(', $this->Identity->get('group_bits'), ')';
		echo $this->Html->link(__('Log out'), [
			'controller'=>'Users', 'action'=>'logout'
				], ['class'=>'button']);
	}
	else {
		echo $this->Html->link(__('Log in'), [
			'controller'=>'Users', 'action'=>'login'
				], ['class'=>'button']);
	};
?>
P &equiv; Q
</div>
