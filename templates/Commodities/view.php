<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Commodity $commodity
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Commodity'), ['action' => 'edit', $commodity->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Commodity'), ['action' => 'delete', $commodity->id], ['confirm' => __('Are you sure you want to delete # {0}?', $commodity->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Commodities'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Commodity'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="commodities view content">
            <h3><?= h($commodity->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($commodity->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Remark') ?></th>
                    <td><?= h($commodity->remark) ?></td>
                </tr>
                <tr>
                    <th><?= __('Tags') ?></th>
                    <td><?= h($commodity->tags) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($commodity->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Home Amount') ?></th>
                    <td><?= $this->Number->format($commodity->home_amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Real Amount') ?></th>
                    <td><?= $this->Number->format($commodity->real_amount) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
