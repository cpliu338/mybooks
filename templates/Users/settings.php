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
<?= $this->Form->create($form, ['id'=>'settings-form']) ?>
<fieldset>
<?php
	echo $this->Form->control('bfDate');
?>
</fieldset>
<?= $this->Form->button(__('Submit'), ['id'=>'submit-settings']) ?>
<?= $this->Form->end() ?>
<div id="result"></div>
<script>
$(function() {
	$("#submit-settings").click(function (ev){
		ev.preventDefault();
		$.ajax({
			url: "<?=$this->url->build(['controller'=>"Users",'action'=>"settings"])?>",
			data: $("#settings-form").serialize(),
			headers: {Accept: "application/json"},
			method: "post"
			
		}).success(function (content) {
			$("#result").html(JSON.stringify(content));
		});
	});
});
</script>
