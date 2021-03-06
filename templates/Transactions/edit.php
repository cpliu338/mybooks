<?php /* 
	$this->start('css');
? >
<style>
.inline-div label {
	display: inline;
}
</style>
< ?php 
	$this->end();
*/?>
<div>
<?= $this->element('tag_chooser', ['ajax'=>true])?>
</div>
<?= $this->Form->create($form, ['id'=>'main-form']) ?>
<fieldset>
<?php
	echo 
	$this->Form->control('tran_date'),
	$this->Form->control('tran_desc');
?>
</fieldset>
<button class="btn-accent" id="add-split">Add split</button>
<button id="edit-commodity" class="btn-accent">
	Edit commodity
</button>
<?= $this->Form->button(__('Confirm'), ['id'=>'confirm', 'disabled'=>true]) ?>
<?= $this->Form->end() ?>
<div id="dlg-edit-commodity" >
	<?= $this->Form->create($commodity, ['class'=>'abbrev-form']) ?>
	<fieldset>
		<?= $this->Form->control('home_amount', ['label'=>'HKD'])?>
		<?= $this->Form->control('id', ['type'=>'select', 'options'=>$commodities, 'label'=>'Currency']) ?>
		<div>= <?=$homeCurrency?></div>
		<?= $this->Form->control('real_amount', ['label'=>'= ' .$homeCurrency])?>
	</fieldset>
	<div id="conversion" data-alternate="abc">def</div>
	<?= $this->Form->button(__('Remember'), ['id'=>'remember', 'type'=>'button']) ?>
	<?= $this->Form->end() ?>
</div>
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
			$("#confirm").attr('disabled', !confirmable());
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
	$("#edit-commodity").click(function (ev){
		ev.preventDefault();
		$("#dlg-edit-commodity").dialog("open");
	});
	$("#dlg-edit-commodity").dialog({
		autoOpen: false,
		title: 'Edit Commodity',
	});
  $( function() {
	setInterval(function(){
		save = $("#conversion").text();
		$("#conversion").text($("#conversion").data('alternate'));
		$("#conversion").data('alternate', save);
	}, 3000);
<?php for ($index=0; $index<count($transaction->entries); $index++): ?>
    addSplit(<?=$index+1?>, <?=$transaction->entries[$index]->id?>,
    	"<?=$transaction->entries[$index]->account->code . ':' . $transaction->entries[$index]->account->name?>",
    	<?=$transaction->entries[$index]->account->id?>,
    	<?=$transaction->entries[$index]->home_amount?>,
    	<?=$transaction->entries[$index]->real_amount?>,
    	"<?=$transaction->entries[$index]->account->currency?>",
    	homeCurrency
    	);
<?php endfor; ?>
  } );
  
  function calcBalance(index) {
  	  i=1;
  	  accum = 0.0;
  	  while (typeof $(`#entry${i}-homeamount`).val() !== "undefined") {
  	  	  if (index != i) {
  	  	  	  accum += $(`#entry${i}-realamount`).val() * $(`#entry${i}-dbcr`).val();
  	  	  }
  	  	  i++;
  	  }
  	  $(`#entry${index}-realamount`).val(0-accum.toFixed(2));
  	  currency = $(`label[for=entry${index}-homeamount]`).text();
  	  homeCurrency = "<?=$homeCurrency?>";
  	  if (currency != homeCurrency) {
			$.ajax({
				url: "<?=$this->url->build(['controller'=>"Commodities",'action'=>"view",
				])?>" + `?name=${encodeURIComponent(currency)}`,
				dataType: "json"
			}).success(function (data){
				realamt = parseFloat($(`#entry${index}-realamount`).val());
				//console.log('index:'+ index);
				homeamt = (realamt * data.commodity.home_amount / data.commodity.real_amount).toFixed(2);
				$(`#entry${index}-msg`).text(`equals ${realamt} ${homeCurrency}`);
				$("#confirm").attr('disabled', !confirmable());
  	  	  $(`#entry${index}-homeamount`).val(Math.abs(homeamt));
  	  	  $(`#entry${index}-dbcr`).val($(`#entry${index}-realamount`).val() > -0.001 ? 1 : -1);
				
				/*
				realamt = (parseFloat($(homeamtid).val()) * data.commodity.real_amount / data.commodity.home_amount).toFixed(2);
				$(msgid).text(`equals ${realamt} ${homeCurrency}`);
				$(realamtid).val(realamt);
				$("#confirm").attr('disabled', !confirmable());*/
			});
  	  }
  	  else {
  	  	  $(`#entry${index}-homeamount`).val(Math.abs($(`#entry${index}-realamount`).val()).toFixed(2));
  	  	  $(`#entry${index}-dbcr`).val($(`#entry${index}-realamount`).val() > -0.001 ? 1 : -1);
  	  }
  }

  function addSplit(index, entryid_val, account_val,
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
	homeamt = $("<input type='number' step='0.01'>");
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
	/*
	labels = $("<div class='inline-div'></div>");
	labels.attr('id', `entry${index}-labels`);
	ar_labels = ['red', 'yellow'];
	ar_labels.forEach(function(value, index) {
		cbox = $(`<input type='checkbox' value='val${index}'>`);
		lbl = $("<label></label>");
		lbl.append(cbox);
		lbl.append(`Value ${value}`);
		labels.append(lbl);
	});
	fieldset.append(labels);
	*/
	$("#main-form fieldset").last().after(fieldset);
    $( "#"+id).autocomplete({
      source: "<?=$this->url->build(['controller'=>"Accounts",'action'=>"suggest"])?>",
      minLength: 1,
      select: function( event, ui) {
      	  //cannot use acc_id because it might have been changed;
      	  $(`#entry${index}-accountid`).val(ui.item.id);
      	  getAccount(ui.item.id, index, 1);
      }
    });
    $("#"+`entry${index}-homeamount`).change(function() {
		if ($(this).val() < -0.001) {
			calcBalance(index);
		}
		calcConversion(index, $("label[for="+`entry${index}-homeamount`+"]").text()); 
		/* this is done in the success closure
		*/
		$("#confirm").attr('disabled', !confirmable());
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
		//console.log('clicked'); 
  	ev.preventDefault();
  	addSplit($("#main-form select").length+1, '', '',
  	  1, '', '',
  	  '', '');
  });
  /*
function addPlaceholder(labels) {
	labels.forEach(function (label, index) {
		id = label.replace("_", "-");
		$("#"+id).attr('placeholder', $("label[for="+id+"]").text());
	});
}
*/
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
	real_cur = "<?=$homeCurrency?>";
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
function confirmable() {
	if ($("#entry1-homeamount").val()<0.005) return false;
	sum = 0.0;
	for (i=1; i<$("fieldset").length; i++) {
		if (Math.abs($(`#entry${i}-dbcr`).val()) !== 1) /* 1 or -1*/
			break;
		if ($(`#entry${i}-accountid`).val() == false) /* 0 or empty */
			return false;
		sum = sum + $(`#entry${i}-realamount`).val()
			* $(`#entry${i}-dbcr`).val();
	} /* 
	if (Math.abs(sum) > 0.005) {
		length = $("fieldset").length;
	} */
	return Math.abs(sum) < 0.005;
}
$("#remember").click(function() {
	data = {
		home_amount: $("#home-amount").val(),
		real_amount: $("#real-amount").val(),
		_method: "PUT"
	};
	$.ajax({
		url: "<?=$this->url->build(['controller'=>"Commodities",'action'=>"edit"])
		?>" + "/" + $("#id").val(),
		method: 'post',
		//contentType: 'x-www-form-urlencoded',
		headers: {
			"X-CSRF-Token": $('meta[name="csrfToken"]').attr('content')},
		dataType: "json",
		data: data
	}).success(function (data) {
		save = data.message;
		$("#conversion").text("OK");
		$("#conversion").data('alternate', data.message);
	});
});
</script>