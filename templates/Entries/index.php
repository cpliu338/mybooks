<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry[]|\Cake\Collection\CollectionInterface $entries
 */
?>
<div class="entries index content">
    <?= $this->Html->link(__('New Entry'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Entries') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('account_id') ?></th>
                    <th><?= $this->Paginator->sort('transaction_id') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('real_amount') ?></th>
                    <th><?= $this->Paginator->sort('home_amount') ?></th>
                    <th><?= $this->Paginator->sort('date2') ?></th>
                    <th><?= $this->Paginator->sort('tags') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                <tr>
                    <td><?= $this->Number->format($entry->id) ?></td>
                    <td><?= $entry->has('account') ? $this->Html->link($entry->account->name, ['controller' => 'Accounts', 'action' => 'view', $entry->account->id]) : '' ?></td>
                    <td><?= $entry->has('transaction') ? $this->Html->link($entry->transaction->id, ['controller' => 'Transactions', 'action' => 'view', $entry->transaction->id]) : '' ?></td>
                    <td><?= h($entry->status) ?></td>
                    <td><?= $this->Number->format($entry->real_amount) ?></td>
                    <td><?= $this->Number->format($entry->home_amount) ?></td>
                    <td><?= h($entry->date2) ?></td>
                    <td>h($entry->tags)</td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $entry->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $entry->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $entry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entry->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
