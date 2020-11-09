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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $entry->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $entry->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Entries'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="entries form content">
            <?= $this->Form->create($entry) ?>
            <fieldset>
                <legend><?= __('Edit Entry') ?></legend>
                <?php
                    echo $this->Form->control('account_id', ['options' => $accounts]);
                    echo $this->Form->control('transaction_id', ['options' => $transactions]);
                    echo $this->Form->control('status');
                    echo $this->Form->control('real_amount');
                    echo $this->Form->control('home_amount');
                    echo $this->Form->control('date2', ['empty' => true]);
                    echo $this->Form->control('labels');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
