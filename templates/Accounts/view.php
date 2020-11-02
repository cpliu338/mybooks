<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>
<div class="row">
    <div class="column-responsive column-100">
        <div class="accounts view content">
            <div id="accordion">
            <h3><?= h($account->name) ?> Details</h3>
            <div>
            	<?= $this->element('account_view')?>
            </div>
            <h3><?= __('Related Tags') ?></h3>
<div>
	<?php if (!empty($account->tags)) : ?>
	<div class="table-responsive">
		<table>
			<tr>
				<th><?= __('Id') ?></th>
				<th><?= __('Name') ?></th>
				<th><?= __('Type') ?></th>
				<th class="actions"><?= __('Actions') ?></th>
			</tr>
			<?php foreach ($account->tags as $tags) : ?>
			<tr>
				<td><?= h($tags->id) ?></td>
				<td><?= h($tags->name) ?></td>
				<td><?= h($tags->type) ?></td>
				<td class="actions">
					<?= $this->Html->link(__('View'), ['controller' => 'Tags', 'action' => 'view', $tags->id]) ?>
					<?= $this->Html->link(__('Edit'), ['controller' => 'Tags', 'action' => 'edit', $tags->id]) ?>
					<?= $this->Form->postLink(__('Delete'), ['controller' => 'Tags', 'action' => 'delete', $tags->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tags->id)]) ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php endif; ?>
</div>
        </div> <!-- accordion -->
			<h3><?php printf("%s entries (%s)", 
                h($account->name),  $account->currency);
                ?></h3>
            <div class="related">
            	<?= $this->Element('account_book', ['account'=>$account,
            			'bf'=>$bf, 'summary'=>false])?>
            </div>
            <button id="transaction-db" class="transaction-dbcr" style="position:fixed; bottom:40px; right:40px">
            	<?= $account->db_label?>
            </button>
            <button id="transaction-cr" class="transaction-dbcr" style="position:fixed; bottom:40px; right:200px" class="btn-accent">
            	<?= $account->cr_label?>
            </button>
        </div> <!-- accounts view content -->
    </div> <!-- column-responsive column-100 -->
</div> <!--row -->
<div id="dlg-form-add" >
</div>
<script>
function popup_dlg(content) {
		$("#dlg-form-add").html(content);
		addPlaceholder(["tran_desc",
			"entry1_accountcode","entry1-realamount",
			"entry2_accountcode"
		]);
	$("#dlg-form-add").dialog("open");
}
$("#transaction-db").click(function (){
	$.ajax({
		url: "<?=$this->url->build(['controller'=>"Transactions",'action'=>"add",
				'?'=>['account_id'=>$account->id,
					'db' => '1']])?>",
	}).done(function (content) {
		popup_dlg(content);
	});
});
$("#transaction-cr").click(function (){
	$.ajax({
		url: "<?=$this->url->build(['controller'=>"Transactions",'action'=>"add",
				'?'=>['account_id'=>$account->id,
					]])?>",
	}).done(function (content) {
		popup_dlg(content);
	});
});
$("#dlg-form-add").dialog({
	autoOpen: false,
	title: 'Create Transaction',
	//width: 600
});
$("#accordion").accordion({
	active: 2,
	collapsible: true,
	event: "click",
});
function addPlaceholder(labels) {
	labels.forEach(function (label, index) {
		id = label.replace("_", "-");
		$("#"+id).attr('placeholder', $("label[for="+id+"]").text());
	});
}
function confirmable() {
	if ($("#entry1-realamount").val()<0.005) return false;
	sum = 0.0;
	for (i=1; i<=$("fieldset").length; i++) {
		if (Math.abs($(`#entry${i}-dbcr`).val()) !== 1) /* 1 or -1*/
			return false;
		else 
				console.log(i + " " + $(`#entry${i}-dbcr`).val());
		if ($(`#entry${i}-accountid`).val() == false) /* 0 or empty */
			return false;
		else 
				console.log(i + " " + $(`#entry${i}-accountid`).val());
		sum = sum + $(`#entry${i}-realamount`).val()
		*$(`#entry${i}-dbcr`).val();
	}
	return Math.abs(sum) < 0.005;
}
</script>