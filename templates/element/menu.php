<nav class="topnav" id="myTopnav">
	<a href="javascript:void(0);" class="" onclick="toggleResponsive()">
		<i class="fa fa-bars"></i>
	</a>
<?php foreach ($menuitems as $item):?>
	<?=$this->Html->link($item['innerHtml'], $item['href'], ['id'=>$item['id']])?>
<?php endforeach;?>
</nav>
