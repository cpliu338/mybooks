<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->Html->meta('csrfToken', $this->request->getAttribute('csrfToken'))?>
    <title>
        <?= __('My books') ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <script
		  src="https://code.jquery.com/jquery-2.2.4.min.js"
		  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
		  crossorigin="anonymous"></script>
    <script 
    	src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js" integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw=" 
    	crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/pepper-grinder/jquery-ui.css">
    
    <?= $this->Html->css(['normalize.min', 'milligram.min', 'cake', 'mybooks']) ?>
    <?= $this->Html->script(['mybooks'])?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
<script>
$(function() {
	$("#settings").dialog({
		autoOpen: false,
		title: 'Settings',
	});
	$("#link-settings").click(function (){
		$.ajax({
			url: "<?=$this->url->build(['controller'=>"Users",'action'=>"settings"
			])?>" + '?redirect=' + encodeURIComponent(window.location.href),
		}).done(function (content) {
			$("#settings").html(content);
			$("#settings").dialog("open");
		});
	});
});
</script>
</head>
<body>
<div style="position:fixed; top:250px; left:115px; opacity:0.5; z-index:99; color:red;">
 	<span style="padding-left:50px"><?=$env?></span>
 	<span style="padding-left:50px"><?=$env?></span>
	 <span style="padding-left:50px"><?=$env?></span>
</div>
	<?= $this->element('menu') ?>
    <div id='settings'></div>
    <main class="main">
        <div class="container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    <footer>
    </footer>
</body>
</html>
