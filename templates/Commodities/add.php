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
            <?= $this->Html->link(__('List Commodities'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="commodities form content">
            <?= $this->Form->create($commodity) ?>
            <fieldset>
                <legend><?= __('Add Commodity') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('remark');
                    echo $this->Form->control('labels');
                    echo $this->Form->control('home_amount');
                    echo $this->Form->control('real_amount');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
