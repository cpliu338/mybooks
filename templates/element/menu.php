<nav class="topnav" id="myTopnav">
<?php foreach ($menuitems as $item):?>
	<?=$this->Html->link($item['innerHtml'], $item['href'], ['id'=>$item['id']])?>
<?php endforeach;?>
	<a href="javascript:void(0);" class="icon" onclick="toggleResponsive()">
		<i class="fa fa-bars"></i>
	</a>
</nav>
