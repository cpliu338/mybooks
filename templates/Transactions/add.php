<?= $this->Form->create($form, ['class'=>'abbrev-form']) ?>
<fieldset>
<?php
	echo $this->Form->control('tran_date'),
	$this->Form->control('tran_desc'),
	$this->Form->control('entry1_accountid', ['type'=>'hidden']),
	$this->Form->control('entry1_accountcode', ['type'=>'hidden']),
	$this->Form->control('entry1_account', ['disabled'=>true]),
	$this->Form->control('entry1_dbcr', ['options'=>$entry1_options]),
	$this->Form->control('entry1_homeamount', [
			'class'=>'amount',
			'label'=>$account1->currency]),
	$this->Form->control('entry1_realamount', ['type'=>'hidden']);
?>
<?php if ($account1->currency != $homeCurrency): ?>
	<div id="entry1-msg"></div>
<script>
	$("#entry1-homeamount").change(function(){
		calcConversion(1, "<?=$account1->currency?>");
	});
</script>
<?php endif; ?>
</fieldset>
<button class="btn-accent" id="add-split">Add split</button>
<?= $this->Form->button(__('Confirm'), ['id'=>'confirm', 'disabled'=>true]) ?>
<?= $this->Form->end() ?>
<script>
	homeCurrency = "<?=$homeCurrency?>";
	function calcConversion(index, currency) {
		msgid = `#entry${index}-msg`;
		homeamtid = `#entry${index}-homeamount`;
		realamtid = `#entry${index}-realamount`;
		if (homeCurrency == currency) {
			$(msgid).text("");
			$(realamtid).val($(homeamtid).val());
		}
		else {
			$.ajax({
				url: "<?=$this->url->build(['controller'=>"Commodities",'action'=>"view",
				])?>" + `?name=${encodeURIComponent(currency)}`,
				dataType: "json"
			}).success(function (data){
				realamt = (parseFloat($(homeamtid).val()) * data.commodity.real_amount / data.commodity.home_amount).toFixed(2);
				$(msgid).text(`equals ${realamt} ${homeCurrency}`);
				$(realamtid).val(realamt);
			});
		}
	}
  function getAccount(id, index) {
  	  $.ajax({
		  url: "<?=$this->url->build(['controller'=>"Accounts",'action'=>"view"])?>"
		  	+ '/' + id,
		  dataType: 'json'
  	  }).done(function(data) {
  	  	  selectId = `#entry${index}-dbcr`;
  	  	  $(selectId).html('');
  	  	  $(selectId).append(new Option(data.account.db_label, -1));
  	  	  $(selectId).append(new Option(data.account.cr_label, 1));
      	  amountid = `entry${index}-homeamount`;
      	  $("label[for="+amountid+"]").text(data.account.currency);
  	  });
  }
  $( function() {
    addSplit();
  } );
  function addSplit() {
  	index = $("#dlg-form-add fieldset").length+1;
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
	homeamt_id = `entry${index}-homeamount`;
	label = $("<label for='"+homeamt_id+"'></label>");
	label.text("Currency");
	fieldset.append(label);
	homeamt = $("<input type='number'>");
	homeamt.attr('id', homeamt_id);
	homeamt.attr('name', `entry${index}_homeamount`);
	homeamt.attr('class', 'amount');
	homeamt.attr('placeholder', `entry${index} homeamount`);
	fieldset.append(homeamt);
	realamt_id = `entry${index}-realamount`;
	realamt = $("<input type='hidden'>");
	realamt.attr('id', realamt_id);
	fieldset.append(realamt);
	div = $("<div></div>");
	div.attr('id', `entry${index}-msg`);
	fieldset.append(div);
	$("#dlg-form-add fieldset").last().after(fieldset);
    $( "#"+id).autocomplete({
      source: "<?=$this->url->build(['controller'=>"Accounts",'action'=>"suggest"])?>",
      minLength: 1,
      select: function( event, ui) {
      	  $("#"+acc_id).val(ui.item.id);
      	  getAccount(ui.item.id, index);
      }
    });
    $("#"+`entry${index}-homeamount`).change(function() {
		$("#confirm").attr('disabled', !confirmable());
		calcConversion(index, $("label[for="+`entry${index}-homeamount`+"]").text()); 
    });
    $("select").change(function() {
		$("#confirm").attr('disabled', !confirmable());
    });
  }
  $("#add-split").click(function (ev){
  	ev.preventDefault();
  	addSplit();
  });

</script>