<?php $this->start('css'); ?>
<style> 
#view-transaction {
	min-width: 400px;
	width:auto !important;
	width: 400px;
}
</style>
<?php $this->end(); ?>
<?php if (!empty($account->entries)) : ?> 
<?php
	$amount = $summary ? 'real_amount' : 'home_amount';
?>
<div id="load-labels">Loading labels ...</div>
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
		<?php foreach ($account->entries as $entry) : ?>
		<tr data-entryid="<?=$entry->id?>">
			<td><?= $entry->transaction->date1->i18nFormat('yyyy-MM-dd') ?></td>
	<?php if ($summary): ?>
			<td>
			<?= $this->Html->link($entry->account->name, ['action'=>'view', 
					$entry->account_id]) ?>
			</td>
	<?php endif; ?>
			<td><?= $this->Html->link($entry->transaction->description,
				['controller'=>'Transactions', 'action'=>'edit', $entry->transaction_id],
				['escape'=>false]
				)?>
			( 
<?php if ($summary): ?>
<?=h($entry->status)?> 
<?php else: ?>
	<a href="#" class="check-entry">
	<?=$entry->status?>
	</a>
<?php endif;?> 
			)</td>
	<?php if ($entry->get($amount)<0): ?>
			<td class="db">
			<?= $this->Number->precision(0-$entry->get($amount),2) ?></td><td class="cr"></td>
	<?php else: ?>
			<td class="db"></td><td class="cr"><?= $this->Number->precision($entry->get($amount),2) ?></td>
	<?php endif; ?>
	<?php $balance += $entry->get($amount); ?>
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
			value="<?= $entry->getAsList('labels')?>">
	</td>
	<?php if (!$summary): ?>
		<td class="actions">
			<?= $this->Html->link('', '#', ['class'=>'fa fa-tag update-labels', 'title'=>__('Update labels')]) ?>
			<?= $this->Html->link('', '#', ['class'=>'fa fa-search view-tran', 'title'=>__('View transaction')]) ?>
			<?= $this->Html->link('', '#', ['class'=>'fa fa-check reconcile', 'title'=>__('Reconcile')]) ?>
			<?= $this->Form->postLink('', ['controller' => 'Entries', 'action' => 'delete', $entry->id], [
					'class'=>'fa fa-minus-circle', 'title'=>__('Delete'),
					'confirm' => __('Are you sure you want to delete # {0}?', $entry->id)]) ?>
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
<div id='check-reconcile' class="table-unresponsive">
	<table>
	<tr><td>Date</td><td id="recon-date">
	</td></tr>
	<tr><td>Balance</td><td id="recon-balance">
	</td></tr>
	<tfoot><tr><td>
	<form method="post" action="<?=$this->url->build(['controller'=>'Entries', 'action'=>'reconcile'])?>">
		<input type="hidden" name="_method" value="POST"/>
		<input type="hidden" name="_csrfToken" autocomplete="off" 
		value='<?=$this->request->getAttribute('csrfToken')?>'/>
		<input type="hidden" name="recon_id" id="recon-id" val="">
		<?= $this->Form->button(__('Submit')) ?>
            </form>
	</td></tr></tfoot>
	</table>
</div>
<div id='view-transaction' class="table-unresponsive">
	<table>
		<thead><tr>
			<th id="tran-date">Account name ...</th>
			<th colspan="3" id="tran-desc">Description ... Description ...</th>
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
	$(".reconcile").click(function() {
		tr = $(this).closest("tr");
		id = tr.data("entryid");
		$("#recon-date").text(tr.children().eq(0).text());
		$("#recon-balance").text(tr.children().eq(4).text());
		$("#recon-id").val(id);
		$("#check-reconcile").dialog("open");
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
	$("#check-reconcile").dialog({
		autoOpen: false,
		title: 'Check reconcile',
		width: "auto"
	});
	$("#view-transaction").dialog({
		autoOpen: false,
		title: 'View trans',
		width: "auto"
	});
	$(".view-tran").click(function (){
		id = $(this).closest("tr").data("entryid");
		view_url = "<?=$this->url->build(['controller'=>"Accounts",'action'=>"view"])?>" + "/";
		$("#view-transaction").dialog({
			title: 'View transaction ' + id,
		});
		$("#tran-date").text("Loading");
		$("#tran-table").empty();
		$.ajax({
			url: "<?=$this->url->build(['controller'=>"Entries",'action'=>"viewPeerEntries"])?>" + "/" + id,
			/*contentType: 'application/json; charset=UTF-8',*/
			headers: {
				Accept: "application/json",
			"X-CSRF-Token": $('meta[name="csrfToken"]').attr('content')},
		  	method: 'get'
		}).success(function (content) {
			entry = content.entry;
			$("#tran-date").text(entry.transaction.date1);
			$("#tran-desc").text(entry.transaction.description);
			row = $("<tr></tr>");
				cell = $("<td></td>");
				cell.html(entry.account.name);
				row.append(cell);
				cell1 = $("<td></td>");
				cell1.html(entry.home_amount < 0.0 ?
					entry.account.db_label :
					entry.account.cr_label);
				row.append(cell1);
				cell2 = $("<td></td>");
				cell2.html(Math.abs(entry.home_amount));
				row.append(cell2);
				cell3 = $("<td></td>");
				cell3.html(entry.account.currency);
				row.append(cell3);
			$("#tran-table").append(row);
			content.peers.forEach(function (peer) {
				row = $("<tr></tr>");
				cell = $("<td></td>");
				anchor = $("<a></a>");
				anchor.attr("href", view_url + peer.account.id);
				anchor.html(peer.account.name);
				anchor.addClass("fa fa-search");
				
				cell.append(anchor);
				row.append(cell);
				cell1 = $("<td></td>");
				cell1.html(peer.home_amount < 0.0 ?
					peer.account.db_label :
					peer.account.cr_label);
				row.append(cell1);
				cell2 = $("<td></td>");
				cell2.html(Math.abs(peer.home_amount));
				row.append(cell2);
				cell3 = $("<td></td>");
				cell3.html(peer.account.currency);
				row.append(cell3);
				$("#tran-table").append(row);
				$("#view-transaction").dialog("open");
			});
		}).error(function (jqXHR, textStatus, errorThrown) {
			$("#tran-desc").text(JSON.stringify(errorThrown));
		});
		$("#view-transaction").dialog("open");
	});
	
		$.ajax({
			url: "<?=$this->url->build(['action'=>'findLabels'])?>/" + 
			"<?= $account->id ?>" + "?summary=" + "<?=$summary?>",
			dataType: 'json',
			contentType: 'application/json; charset=UTF-8',
			headers: {
			"X-CSRF-Token": $('meta[name="csrfToken"]').attr('content')},
		  	method: 'get'
		}).success(function (content) {
			//console.log(content.balance);
			$("#load-labels").html("Labels: " + 
				content.labels.sort().join(", "));
		}).error(function (jqXHR, textStatus, errorThrown) {
			$("#load-labels").html(JSON.stringify(errorThrown));
		});
});
</script>