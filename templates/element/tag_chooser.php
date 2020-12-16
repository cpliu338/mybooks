<ul id="toggleable">
<li data-id="0">Any</li>
<?php foreach ($tags as $id=>$name): ?>
<li data-id="<?= $id?>"> <?= $name?></li>
<?php endforeach; ?>
</ul>
<button id="reload">Reload</button>
<span id="result"></span>
<script>
var selected = [<?php echo join($tagfilter, ',')?>];
$(function () {
	$("#toggleable li").each(function (index){
		if (selected.includes($(this).data('id'))) 
			$(this).addClass("ui-selected");
	});
	$("#toggleable li").click(function() {
		found = false;
		for (var i = 0; i<selected.length && !found; i++) {
			if (selected[i] === $(this).data('id')) {
				selected.splice(i, 1);
				found = true;
			}
		}
		if (!found) selected.push($(this).data('id'));
		$(this).toggleClass("ui-selected");
		$("#reload").attr('disabled', false);
	});
	$("#reload").click(function() {
		$.ajax({
			url: "<?=$this->url->build(['controller'=>"Accounts",'action'=>"setFilter"])?>",
			dataType: 'json',
			contentType: 'application/json; charset=UTF-8',
			data: JSON.stringify(selected),
			headers: {/*Accept: "application/json",*/
			"X-CSRF-Token": $('meta[name="csrfToken"]').attr('content')},
		  	method: 'post'
		}).success(function (content) {
<?php if ($ajax): ?>		
			//console.log(content);
			$("#reload").attr('disabled', true);
<?php else: ?>		
			location.reload();
<?php endif; ?>		
		}).error(function (jqXHR, textStatus, errorThrown) {
			$("#result").html(JSON.stringify(errorThrown));
		});
	});
});	
</script>
