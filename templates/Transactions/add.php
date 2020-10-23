<?php
use Cake\Routing\Router;
?>
<?= $this->Form->create($form, ['id'=>'abbrev-form']) ?>
<fieldset>
<?php
	echo $this->Form->control('tran_date'),
	$this->Form->control('tran_desc'),
	$this->Form->control('entry1_accountid', ['type'=>'hidden']),
	$this->Form->control('entry1_accountcode', ['type'=>'hidden']),
	$this->Form->control('entry1_account', ['value'=>$account1->code . ':' . $account1->name]),
	$this->Form->control('entry1_dbcr', ['options'=>$entry1_options]),
	$this->Form->control('entry1_realamount');
?>
</fieldset>
<button class="btn-accent" id="add-split">Add split</button>
<?= $this->Form->button(__('Confirm'), ['id'=>'confirm', 'disabled'=>true]) ?>
<?= $this->Form->end() ?>
<script>
  function getAccount(id, index) {
  	  $.ajax({
		  url: "<?=Router::url(['controller'=>"Accounts",'action'=>"view"])?>"
		  	+ '/' + id,
		  dataType: 'json'
  	  }).done(function(data) {
  	  	  selectId = `#entry${index}-dbcr`;
  	  	  $(selectId).html('');
  	  	  $(selectId).append(new Option(data.account.db_label, -1));
  	  	  $(selectId).append(new Option(data.account.cr_label, 1));
  	  });
  }
  $( function() {
    addSplit();
  } );
  function addSplit() {
  	index = $("fieldset").length+1;
  	clazz = index%2 ? '' : 'even-row';
	fieldset = $(`<fieldset class="${clazz}"></fieldset>`);
	acc = $("<input>");
	id = `entry${index}-account`;
	acc.attr('id', id);
	acc.attr('name', `entry${index}_account`);
	acc.attr('placeholder', `entry${index} account`);
	fieldset.append(acc);
	acc2 = $("<input type='hidden'>");
	acc_id = `entry${index}-accountid`;
	acc2.attr('id', acc_id);
	acc2.attr('name', `entry${index}_accountid`);
	fieldset.append(acc2);
	select = $("<select></select>");
	select.attr('id', `entry${index}-dbcr`);
	select.attr('name', `entry${index}_dbcr`);
	fieldset.append(select);
	realamt = $("<input type='number'>");
	realamt.attr('id', `entry${index}-realamount`);
	realamt.attr('name', `entry${index}_realamount`);
	realamt.attr('placeholder', `entry${index} realamount`);
	fieldset.append(realamt);
	$("fieldset").last().after(fieldset);
    $( "#"+id).autocomplete({
      source: "<?=Router::url(['controller'=>"Accounts",'action'=>"suggest"])?>",
      minLength: 1,
      select: function( event, ui) {
      	  $("#"+acc_id).val(ui.item.id);
      	  getAccount(ui.item.id, index);
      }
    });
    $("#"+`entry${index}-realamount`).blur(function() {
		$("#confirm").attr('disabled', !confirmable());
    });
  }
  $("#add-split").click(function (ev){
  	ev.preventDefault();
  	addSplit();
  });

</script>