<?php if (!empty($account->entries)) : ?> 
<?php
	$amount = $summary ? 'real_amount' : 'home_amount';
?>
<div class="table-responsive">
	<table>
		<thead><tr>
			<th><?= __('Date') ?></th>
	<?php if ($summary): ?>
			<th><?= __('Account') ?></th>
	<?php endif; ?>
			<th><?= __('Transaction (status)') ?></th>
			<th><?= $account->db_label ?></th>
			<th><?= $account->cr_label ?></th>
			<th><?= __('Balance') ?></th>
			<th><?= __('Tags') ?></th>
	<?php if (!$summary): ?>
			<th class="actions"><?= __('Actions') ?></th>
	<?php endif; ?>
		</tr></thead><tbody>
		<tr>
			<td><?= $bfDate ?></td><td>Brought Forward</td>
	<?php if ($summary): ?>
			<td></td>
	<?php endif; ?>
			<td></td><td></td>
			<td><?= $bf ?></td>
			<td></td><td></td>
		</tr>
		<?php $balance = $bf; ?>
		<?php foreach ($account->entries as $entries) : ?>
		<tr>
			<td><?= $entries->transaction->date1->i18nFormat('yyyy-MM-dd') ?></td>
	<?php if ($summary): ?>
			<td><?= h($entries->account->name) ?></td>
	<?php endif; ?>
			<td><?= h($entries->transaction->description) ?>
			( <?= h($entries->status) ?> )</td>
	<?php if ($entries->get($amount)<0): ?>
			<td class="db"><?= 0-$entries->get($amount) ?></td><td class="cr"></td>
	<?php else: ?>
			<td class="db"></td><td class="cr"><?= $entries->get($amount) ?></td>
	<?php endif; ?>
	<?php $balance += $entries->get($amount); ?>
			<td class="balance">
	<?php if (strpos('14', substr($account->code, 0, 1)) === false): ?>
			<?= $balance<=0 ? 0-$balance : "$balance DB" ?>
	<?php else: ?>
			<?= $balance>=0 ? "$balance CR" : 0 - $balance ?>
	<?php endif ?>
			</td>
			<td> h($entries->tags) </td>
	<?php if (!$summary): ?>
			<td class="actions">
				<?= $this->Html->link(__('View'), ['controller' => 'Entries', 'action' => 'view', $entries->id]) ?>
				<?= $this->Html->link(__('Edit'), ['controller' => 'Entries', 'action' => 'edit', $entries->id]) ?>
				<?= $this->Form->postLink(__('Delete'), ['controller' => 'Entries', 'action' => 'delete', $entries->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entries->id)]) ?>
			</td>
	<?php endif; ?>
		</tr>
		<?php endforeach; ?>
	</tbody></table>
</div> <!-- table responsive -->
<?php endif; ?>
