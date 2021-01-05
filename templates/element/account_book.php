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
			<td class="balance"><?= $this->Number->precision($bf,2) ?></td>
			<td></td><td></td>
		</tr>
		<?php $balance = $bf; ?>
		<?php foreach ($account->entries as $entries) : ?>
		<tr>
			<td><?= $entries->transaction->date1->i18nFormat('yyyy-MM-dd') ?></td>
	<?php if ($summary): ?>
			<td>
			<?= $this->Html->link($entries->account->name, ['action'=>'view', 
					$entries->account_id]) ?>
			</td>
	<?php endif; ?>
			<td><?= $this->Html->link(h($entries->transaction->description),
				['controller'=>'Transactions', 'action'=>'edit', $entries->transaction_id])?>
			( 
<?php if ($summary): ?>
<?=h($entries->status)?> 
<?php else: ?>
	<a href="#" class="check-entry" data-entryid="<?=$entries->id?>">
	<?=$entries->status?>
	</a>
<?php endif;?> 
			)</td>
	<?php if ($entries->get($amount)<0): ?>
			<td class="db">
			<?= $this->Number->precision(0-$entries->get($amount),2) ?></td><td class="cr"></td>
	<?php else: ?>
			<td class="db"></td><td class="cr"><?= $this->Number->precision($entries->get($amount),2) ?></td>
	<?php endif; ?>
	<?php $balance += $entries->get($amount); ?>
			<td class="balance">
	<?php if (strpos('14', substr($account->code, 0, 1)) === false): ?>
			<?= $balance<=0 ? $this->Number->precision(0-$balance,2) : $this->Number->precision($balance,2) . " DB" ?>
	<?php else: ?>
			<?= $balance>=0 ? $this->Number->precision($balance, 2) . " CR" : $this->Number->precision(0 - $balance,2) ?>
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
	</tbody>
	<tfoot>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	</tr></tfoot>
	</table>
</div> <!-- table responsive -->
<?php endif; ?>
<script>
	$(".check-entry").click(function() {
		element = $(this);
		element.html("...");
		$.ajax({
			url: "<?=$this->url->build(['controller'=>'Entries', 'action'=>'check'])?>"+
			"/"+$(this).data("entryid"),
			headers: {
			"X-CSRF-Token": $('meta[name="csrfToken"]').attr('content'),
			"Accept": "application/json"},
		  	method: 'post'
		}).success(function (content) {
			element.html(content.status);
		}).error(function (jqXHR, textStatus) {
			element.html(textStatus);
		});
	});			
</script>