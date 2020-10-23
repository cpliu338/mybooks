<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
use Cake\Routing\Router;
?>
<div class="row">
    <div class="column-responsive column-100">
        <div class="accounts view content">
            <div id="accordion">
            <h3><?= h($account->name) ?> Details</h3>
            	<?= $this->element('account_view')?>
            <div>
            <div class="related">
            </div>
                <h3><?= __('Related Entries') ?></h3>
            <div class="related">
                <?php if (!empty($account->entries)) : ?>
                <div class="table-responsive">
                    <table>
                        <thead><tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Account Id') ?></th>
                            <th><?= __('Transaction Id') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Real Amount') ?></th>
                            <th><?= __('Home Amount') ?></th>
                            <th><?= __('Date2') ?></th>
                            <th><?= __('Tags') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr></thead><tbody>
                        <?php foreach ($account->entries as $entries) : ?>
                        <tr>
                            <td><?= h($entries->id) ?></td>
                            <td><?= h($entries->account_id) ?></td>
                            <td><?= h($entries->transaction_id) ?></td>
                            <td><?= h($entries->status) ?></td>
                            <td><?= h($entries->real_amount) ?></td>
                            <td><?= h($entries->home_amount) ?></td>
                            <td><?= h($entries->date2) ?></td>
                            <td><?= h($entries->tags) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Entries', 'action' => 'view', $entries->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Entries', 'action' => 'edit', $entries->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Entries', 'action' => 'delete', $entries->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entries->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody></table>
                </div>
                <?php endif; ?>
            </div>
            </div>
            <button id="transaction-db" class="transaction-dbcr" style="position:fixed; bottom:40px; right:40px">
            	<?= $account->db_label?>
            </button>
            <button id="transaction-cr" class="transaction-dbcr" style="position:fixed; bottom:40px; right:200px" class="btn-accent">
            	<?= $account->cr_label?>
            </button>
        </div>
    </div>
</div>
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
		url: "<?=Router::url(['controller'=>"Transactions",'action'=>"add",
				'?'=>['account_id'=>$account->id,
					'db' => '1']])?>",
	}).done(function (content) {
		popup_dlg(content);
	});
});
$("#transaction-cr").click(function (){
	$.ajax({
		url: "<?=Router::url(['controller'=>"Transactions",'action'=>"add",
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