<?= $this->Form->create($form, ['class'=>'abbrev-form', 'id'=>'form-add']) ?>
<fieldset>
<?php
	echo 
	$this->Form->control('tran_date'),
	$this->Form->control('tran_desc');
?>
</fieldset>
<button class="btn-accent" id="add-split">Add split</button>
<?= $this->Form->button(__('Confirm'), ['id'=>'confirm', 'disabled'=>true]) ?>
<?= $this->Form->end() ?>
<script>
	homeCurrency = "<?=$homeCurrency?>";
	function calcConversion(index, currency) {
  	  //console.log("calcConversion");
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
				$("#confirm").attr('disabled', !confirmable());
			});
		}
	}
  function getAccount(id, index, homeamount_val) {
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
  	  }).then(function() {
    //console.log($(`#entry${index}-dbcr`+" option").length);
  	  	  $(`#entry${index}-dbcr`).val(homeamount_val>0 ? 1 : -1);
  	  	  $("#confirm").attr('disabled', !confirmable());    
  	  });
  }
  $( function() {
<?php for ($index=0; $index<count($transaction->entries); $index++): ?>
    addOldSplit(<?=$index+1?>, <?=$transaction->entries[$index]->id?>,
    	"<?=$transaction->entries[$index]->account->code . ':' . $transaction->entries[$index]->account->name?>",
    	<?=$transaction->entries[$index]->account->id?>,
    	<?=$transaction->entries[$index]->home_amount?>,
    	<?=$transaction->entries[$index]->real_amount?>,
    	"<?=$transaction->entries[$index]->account->currency?>",
    	homeCurrency
    	);
<?php endfor; ?>
    /*
    $("#entry1-homeamount").change(function() {
		calcConversion(1, $("label[for=entry1-homeamount]").text()); 
		$("#confirm").attr('disabled', !confirmable());
    });
    */
  } );
  function addSplit(index) {
  }
  function addOldSplit(index, entryid_val, account_val,
  	  accountid_val, homeamount_val, realamount_val,
  	  currency_val, homecurrency_val) {
  	clazz = index%2 ? '' : 'even-row';
	fieldset = $(`<fieldset class="${clazz}"></fieldset>`);
	entryid = $("<input type='hidden'>");
	entryid.attr('id', `entry${index}-id`);
	entryid.attr('name', `entry${index}_id`);
	entryid.attr('value', entryid_val);
	fieldset.append(entryid);
	acc = $("<input>");
	id = `entry${index}-account`;
	acc.attr('id', id);
	acc.attr('name', `entry${index}_account`);
	acc.attr('placeholder', `entry${index} account`);
	acc.attr('value', account_val);
	fieldset.append(acc);
	acc2 = $("<input type='hidden'>");
	acc_id = `entry${index}-accountid`;
	acc2.attr('id', acc_id);
	acc2.attr('name', `entry${index}_accountid`);
	acc2.attr('value', accountid_val);
	fieldset.append(acc2);
	select = $("<select></select>");
	select.attr('id', `entry${index}-dbcr`);
	select.attr('name', `entry${index}_dbcr`);
	fieldset.append(select);
	homeamt_id = `entry${index}-homeamount`;
	label = $("<label for='"+homeamt_id+"'></label>");
	label.text(currency_val);
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
	realamt.attr('name', `entry${index}_realamount`);
	fieldset.append(realamt);
	div = $("<div></div>");
	div.attr('id', `entry${index}-msg`);
	div.text(`Equals ${homecurrency_val} ${Math.abs(realamount_val)}`);
	fieldset.append(div);
	$("#form-add fieldset").last().after(fieldset);
    $( "#"+id).autocomplete({
      source: "<?=$this->url->build(['controller'=>"Accounts",'action'=>"suggest"])?>",
      minLength: 1,
      select: function( event, ui) {
      	  $("#"+acc_id).val(ui.item.id);
      	  getAccount(ui.item.id, index, 1);
      }
    });
    $("#"+`entry${index}-homeamount`).change(function() {
		calcConversion(index, $("label[for="+`entry${index}-homeamount`+"]").text()); 
		/* this is done in the success closure
		$("#confirm").attr('disabled', !confirmable());
		*/
    });
    $("select").change(function() {
		$("#confirm").attr('disabled', !confirmable());
    });
    getAccount(accountid_val, index, homeamount_val); 
//	$(`#entry${index}-dbcr`).val(homeamount_val>0 ? 1 : -1);
	homeamt.attr('value', Math.abs(homeamount_val));
	realamt.attr('value', Math.abs(realamount_val));
  }
  $("#add-split").click(function (ev){
  	ev.preventDefault();
  	addSplit();
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