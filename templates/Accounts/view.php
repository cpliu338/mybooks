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
            <button id="edit-commodity" class="btn-accent" style="position:fixed; bottom:40px; right:360px">
            	Edit commodity
            </button>
            <button id="transaction-db" class="transaction-dbcr" style="position:fixed; bottom:40px; right:40px">
            	<?= $account->db_label?>
            </button>
            <button id="transaction-cr" class="transaction-dbcr" style="position:fixed; bottom:40px; right:200px">
            	<?= $account->cr_label?>
            </button>
        </div> <!-- accounts view content -->
    </div> <!-- column-responsive column-100 -->
</div> <!--row -->
<div id="dlg-edit-commodity" >
	<?= $this->Form->create($commodity, ['class'=>'abbrev-form']) ?>
	<fieldset>
		<?= $this->Form->control('home_amount', ['label'=>$account->currency])?>
		<?= $this->Form->control('id', ['type'=>'select', 'options'=>$commodities]) ?>
		<div>= <?=$currency?></div>
		<?= $this->Form->control('real_amount', ['label'=>'= ' .$currency])?>
	</fieldset>
	<div id="conversion" data-alternate="abc">def</div>
	<?= $this->Form->button(__('Remember'), ['id'=>'remember', 'type'=>'button']) ?>
	<?= $this->Form->end() ?>
</div>
<div id="dlg-form-add" >
</div>
<script>
$(function(){
	setInterval(function(){
		save = $("#conversion").text();
		$("#conversion").text($("#conversion").data('alternate'));
		$("#conversion").data('alternate', save);
	}, 3000);
});
$("#home-amount").change(function(){
	conversion();
});
$("#real-amount").change(function(){
	conversion();
});
$("#id").change(function(){
	$.ajax({
		url: "<?=$this->url->build(['controller'=>"Commodities",'action'=>"view"])
		?>" + "/" + $("#id").val(),
		dataType: "json"
	}).success(function (data) {
		$("#real-amount").val(data.commodity.real_amount);
		$("#home-amount").val(data.commodity.home_amount);
		conversion();
	});
});
function conversion() {
	real_cur = "<?=$currency?>";
	$( "#id option:selected" ).each(function() {
		home_cur = $(this).text();
	});
	home_amount = $("#home-amount").val();
	real_amount = $("#real-amount").val();
	$("#conversion").text("1 " + home_cur + " = " + 
		(real_amount/home_amount) + " " + real_cur);
	$("#conversion").data('alternate', "1 " + real_cur + " = " + 
		(home_amount/real_amount) + " " + home_cur);
}
function popup_dlg(content) {
	$("#dlg-form-add").html(content);
	addPlaceholder(["tran_desc",
		"entry1_account", "entry1_homeamount"
	]);
	$("#dlg-form-add").dialog("open");
	$("form").find("label").each(function(index) {
		if (! $(this).attr("for").endsWith('homeamount') )
			$(this).addClass("hide");
	});
}
$("#edit-commodity").click(function (){
	$("#dlg-edit-commodity").dialog("open");
});
$("#transaction-db").click(function (){
	$.ajax({
		url: "<?=$this->url->build(['controller'=>"Transactions",'action'=>"add",
				'?'=>['account_id'=>$account->id,
					'db' => '1']], ['escape'=>false])?>",
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
});
$("#dlg-edit-commodity").dialog({
	autoOpen: false,
	title: 'Edit Commodity',
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
	if ($("#entry1-homeamount").val()<0.005) return false;
	sum = 0.0;
	for (i=1; i<$("fieldset").length; i++) {
		if (Math.abs($(`#entry${i}-dbcr`).val()) !== 1) /* 1 or -1*/
			return false;
		if ($(`#entry${i}-accountid`).val() == false) /* 0 or empty */
			return false;
		sum = sum + $(`#entry${i}-realamount`).val()
			* $(`#entry${i}-dbcr`).val();
	}/*
	if (Math.abs(sum) > 0.005) {
		length = $("fieldset").length;
		console.log(`Fieldsets: ${length} sum: ${sum}`);
	} */
	return Math.abs(sum) < 0.005;
}
</script>