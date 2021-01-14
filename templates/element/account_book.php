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
			<th><?= __('Labels') ?></th>
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
			<td class="balance">
<?php 
	switch (substr($account->code, 0, 1)) {
	case '1':
			echo $bf<=0.001 ? $this->Number->precision(0-$bf,2) : $this->Number->precision($bf,2) . " CR";
			break;      
	case '5':
			echo $bf>=0.001 ? $this->Number->precision($bf,2) . ' CR' : $this->Number->precision(0-$bf,2);
			break;
	default:
			echo $bf>=-0.001 ? $this->Number->precision($bf,2) : $this->Number->precision(0-$bf,2) . " DB";
	}
?>
			</td>
			<td></td><td></td>
		</tr>
		<?php $balance = $bf; ?>
		<?php foreach ($account->entries as $entries) : ?>
		<tr data-entryid="<?=$entries->id?>">
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
	<a href="#" class="check-entry">
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
<?php 
	switch (substr($account->code, 0, 1)) {
	case '1':
			echo $balance<=0.001 ? $this->Number->precision(0-$balance,2) : $this->Number->precision($balance,2) . " CR";
			break;
	case '5':
			echo $balance>=0.001 ? $this->Number->precision($balance, 2) . " CR" : $this->Number->precision(0 - $balance,2);
			break;
	default:
			echo $balance>-0.001 ? $this->Number->precision($balance,2) : $this->Number->precision(0-$balance,2) . " DB";
	}
?>
			</td>
			<td><input class="labels"
			value="<?= $entries->getAsList('labels')?>">
	</td>
	<?php if (!$summary): ?>
			<td class="actions">
<?= $this->Html->link('', '#', ['class'=>'fa fa-tag update-labels', 'title'=>__('Update labels')]) ?>
<?= $this->Html->link('', '#', ['class'=>'fa fa-search view', 'title'=>__('View transaction')]) ?>
				<?= $this->Form->postLink('', ['controller' => 'Entries', 'action' => 'delete', $entries->id], [
						'class'=>'fa fa-minus-circle', 'title'=>__('Delete'),
						'confirm' => __('Are you sure you want to delete # {0}?', $entries->id)]) ?>
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
<div id='view-transaction' class="table-unresponsive">
	<table>
		<thead><tr>
			<th id="tran-date">Date</th>
			<th id="tran-desc">Desc</th>
		</tr></thead>
		<tbody id="tran-table">
		</tbody>
	</table>
</div>
<script>
	$(".check-entry").click(function() {
		element = $(this);
		element.html("...");
		entryid = $(this).parent().parent().data("entryid");
		$.ajax({
			url: "<?=$this->url->build(['controller'=>'Entries', 'action'=>'check'])?>"+
			"/"+entryid,
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
	$(".update-labels").click(function() {
		element = $(this);
		entryid = $(this).parent().parent().data("entryid");
		input = $(this).parent().prev().find('input');
		data = {labels: $(this).parent().prev().find('input').val()};
		  		//$(this).removeClass(['fa-tag']);return;
		$.ajax({
			url: "<?=$this->url->build(['action'=>'updateLabels','controller'=>'Entries'])?>/" + entryid,
			dataType: 'json',
			data: JSON.stringify(data),
			contentType: 'application/json; charset=UTF-8',
			headers: {
			"X-CSRF-Token": $('meta[name="csrfToken"]').attr('content')},
		  	method: 'post',
		  	beforeSend: function (jqXHR) {
		  		element.removeClass('fa-tag fa-exclamation');
		  		element.addClass('fa-ellipsis-h');
		  	}
		}).success(function (content) {
			input.val(content.labels.join(" "));
			element.prop('title', 'Update labels');
			element.removeClass('fa-ellipsis-h');
			element.addClass('fa-tag');
		}).error(function (jqXHR, textStatus, errorThrown) {
			element.prop('title', JSON.stringify(errorThrown));
			element.removeClass('fa-ellipsis-h');
			element.addClass('fa-exclamation');
		});
	});			
$(function() {
	$("#view-transaction").dialog({
		autoOpen: false,
		title: 'View trans',
	});
	$(".view").click(function (){
		$("#view-transaction").dialog({
			title: 'View transaction',
		});
		$("#view-transaction").dialog("open");
	});
});
</script>