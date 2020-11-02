<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
use Cake\Routing\Router;
use Cake\Core\Configure;
?>
<div class="row">
    <div class="column-responsive column-100">
        <div class="accounts view content">
			<h3><?php printf("%s summary (%s)", 
                h($account->name),  Configure::read('HomeCurrency'));
                ?></h3><?= $bf ?>
            <div class="related">
            	<?= $this->Element('account_book', ['account'=>$account, 
            			'bf'=>$bf, 'summary'=>true])?>
            </div>
        </div> <!-- accounts view content -->
    </div> <!-- column-responsive column-100 -->
</div> <!--row -->
