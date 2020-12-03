<?= $this->Form->create($form, ['class'=>'abbrev-form2', 'id'=>'form-add']) ?>
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
<!-- script>
	$("#entry1-homeamount").change(function(){
		calcConversion(1, "<?=$account1->currency?>");
	});
</script -->
<?php endif; ?>
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
	$("#edit-commodity").click(function (ev){
		ev.preventDefault();
		$("#dlg-edit-commodity").dialog("open");
	});
	$("#dlg-edit-commodity").dialog({
		autoOpen: false,
		title: 'Edit Commodity',
	});
	homeCurrency = "<?=$homeCurrency?>";
	function calcConversion(index, currency) {
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
	$("#tran-desc").attr('placeholder', 'Description');
    $("#entry1-homeamount").change(function() {
		calcConversion(1, $("label[for=entry1-homeamount]").text()); 
		$("#confirm").attr('disabled', !confirmable());
    });
	setInterval(function(){
		save = $("#conversion").text();
		$("#conversion").text($("#conversion").data('alternate'));
		$("#conversion").data('alternate', save);
	}, 3000);
  } );
  function addSplit() {
  	index = $("#form-add fieldset").length+1;
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
	realamt.attr('name', `entry${index}_realamount`);
	fieldset.append(realamt);
	div = $("<div></div>");
	div.attr('id', `entry${index}-msg`);
	fieldset.append(div);
	$("#form-add fieldset").last().after(fieldset);
    $( "#"+id).autocomplete({
      source: "<?=$this->url->build(['controller'=>"Accounts",'action'=>"suggest"])?>",
      minLength: 1,
      select: function( event, ui) {
      	  $("#"+acc_id).val(ui.item.id);
      	  getAccount(ui.item.id, index);
      }
    });
    $("#"+`entry${index}-homeamount`).change(function() {
  	  console.log($(this).attr('id'));
		calcConversion(index, $("label[for="+`entry${index}-homeamount`+"]").text()); 
		/* this is done in the success closure
		$("#confirm").attr('disabled', !confirmable());
		*/
    });
    $("select").change(function() {
		$("#confirm").attr('disabled', !confirmable());
    });
  }
  $("#add-split").click(function (ev){
  	ev.preventDefault();
  	addSplit();
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
		console.log(`sum: ${sum}`); 
	} 
	if (Math.abs(sum) > 0.005) {
		length = $("fieldset").length;
	} /* */
	return Math.abs(sum) < 0.005;
}

</script>