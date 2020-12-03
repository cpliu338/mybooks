<?php 
	if ($this->Identity->isLoggedIn()) {
		echo $this->Identity->get('name'), '(', $this->Identity->get('group_bits'), ')';
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
	echo $this->Form->control('bfDate'),
	$this->Form->control('redirect', ['type'=>'hidden']);
?>
</fieldset>
<?= $this->Form->button(__('Submit'), ['id'=>'submit-settings']) ?>
<?= $this->Form->end() ?>
<div id="result"></div>
<script>
$(function() {
	$("#submit-settings2").click(function (ev){
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
