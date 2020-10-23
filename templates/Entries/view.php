<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry $entry
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Entry'), ['action' => 'edit', $entry->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Entry'), ['action' => 'delete', $entry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entry->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Entries'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Entry'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="entries view content">
            <h3><?= h($entry->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Account') ?></th>
                    <td><?= $entry->has('account') ? $this->Html->link($entry->account->name, ['controller' => 'Accounts', 'action' => 'view', $entry->account->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Transaction') ?></th>
                    <td><?= $entry->has('transaction') ? $this->Html->link($entry->transaction->id, ['controller' => 'Transactions', 'action' => 'view', $entry->transaction->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($entry->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Tags') ?></th>
                    <td><?= h($entry->tags) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($entry->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Real Amount') ?></th>
                    <td><?= $this->Number->format($entry->real_amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Home Amount') ?></th>
                    <td><?= $this->Number->format($entry->home_amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Date2') ?></th>
                    <td><?= h($entry->date2) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
