<div id='account-tray'>
<?php foreach ($tray_accounts as $tray_account): ?>
	<?= $this->Html->link($tray_account->name, [
		'action'=>'view', $tray_account->id	
	], ['class'=>'button'])?>
<?php endforeach;?>
</div>
