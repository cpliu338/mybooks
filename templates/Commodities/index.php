<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Commodity[]|\Cake\Collection\CollectionInterface $commodities
 */
?>
<div class="commodities index content">
    <?= $this->Html->link(__('New Commodity'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Commodities') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('remark') ?></th>
                    <th><?= $this->Paginator->sort('tags') ?></th>
                    <th><?= $this->Paginator->sort('home_amount') ?></th>
                    <th><?= $this->Paginator->sort('real_amount') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commodities as $commodity): ?>
                <tr>
                    <td><?= $this->Number->format($commodity->id) ?></td>
                    <td><?= h($commodity->name) ?></td>
                    <td><?= h($commodity->remark) ?></td>
                    <td><?= h($commodity->tags) ?></td>
                    <td><?= $this->Number->format($commodity->home_amount) ?></td>
                    <td><?= $this->Number->format($commodity->real_amount) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $commodity->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $commodity->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $commodity->id], ['confirm' => __('Are you sure you want to delete # {0}?', $commodity->id)]) ?>
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
