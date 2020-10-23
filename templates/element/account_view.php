<table>
	<tr>
		<th><?= __('Name') ?></th>
		<td><?= h($account->name) ?></td>
	</tr>
	<tr>
		<th><?= __('Code') ?></th>
		<td><?= h($account->code) ?></td>
	</tr>
	<tr>
		<th><?= __('Db Label') ?></th>
		<td><?= h($account->db_label) ?></td>
	</tr>
	<tr>
		<th><?= __('Cr Label') ?></th>
		<td><?= h($account->cr_label) ?></td>
	</tr>
	<tr>
		<th><?= __('Currency') ?></th>
		<td><?= h($account->currency) ?></td>
	</tr>
</table>
	<h4><?= __('Related Tags') ?></h4>
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
