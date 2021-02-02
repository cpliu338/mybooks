<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>
<?= $this->element('account_tray')?>
<div class="row">
    <div class="column-responsive column-100">
        <div class="accounts view content">
            <div id="accordion">
            <h3><?= h($account->name) ?> Details</h3>
            <div>
            	<?= $this->element('account_view')?>
            </div>
            <h3><?= __('Related Tags') ?></h3>
<div>
	<?php if (!empty($account->tags)) : ?>
	<div class="table-responsive">
		<table>
			<tr>
				<th><?= __('Id') ?></th>
				<th><?= __('Name') ?></th>
				<th><?= __('Type') ?></th>
				<th class="actions"><?= __('Actions') ?></th>
			</tr>
			<?php foreach ($account->tags as $tags) : ?>
			<tr>
				<td><?= h($tags->id) ?></td>
				<td><?= h($tags->name) ?></td>
				<td><?= h($tags->type) ?></td>
				<td class="actions">
					<?= $this->Html->link(__('View'), ['controller' => 'Tags', 'action' => 'view', $tags->id]) ?>
					<?= $this->Html->link(__('Edit'), ['controller' => 'Tags', 'action' => 'edit', $tags->id]) ?>
					<?= $this->Form->postLink(__('Delete'), ['controller' => 'Tags', 'action' => 'delete', $tags->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tags->id)]) ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php endif; ?>
</div>
        </div> <!-- accordion -->
			<h3><?php printf("%s entries (%s)", 
                h($account->name),  $account->currency);
                ?></h3>
            <div class="related">
            	<?= $this->Element('account_book', ['account'=>$account,
            			'bf'=>$bf, 'summary'=>false])?>
            </div>
    <?= $this->Html->link(__('New'), ['action' => 'add', 'controller'=>'Transactions', 
    		'?'=>['account_id'=>$account->id]], ['class' => 'button float-right'
    		]) ?>
<?php if ($account->earnsInterest()) :?>
    <?= $this->Html->link(__('New Interest'), ['action' => 'earnsInterest', 'controller'=>'Transactions', 
    		'?'=>['account_id'=>$account->id]], ['class' => 'button float-left'
    		]) ?>
<?php endif; ?>
        </div> <!-- accounts view content -->
    </div> <!-- column-responsive column-100 -->
</div> <!--row -->
<script>
$("#accordion").accordion({
	active: 2,
	collapsible: true,
	event: "click",
});
</script>