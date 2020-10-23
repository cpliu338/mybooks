<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tag $tag
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Tag'), ['action' => 'edit', $tag->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Tag'), ['action' => 'delete', $tag->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tag->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Tags'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Tag'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="tags view content">
            <h3><?= h($tag->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($tag->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Type') ?></th>
                    <td><?= h($tag->type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($tag->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Accounts') ?></h4>
                <?php if (!empty($tag->accounts)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Code') ?></th>
                            <th><?= __('Db Label') ?></th>
                            <th><?= __('Cr Label') ?></th>
                            <th><?= __('Currency') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($tag->accounts as $accounts) : ?>
                        <tr>
                            <td><?= h($accounts->id) ?></td>
                            <td><?= h($accounts->name) ?></td>
                            <td><?= h($accounts->code) ?></td>
                            <td><?= h($accounts->db_label) ?></td>
                            <td><?= h($accounts->cr_label) ?></td>
                            <td><?= h($accounts->currency) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Accounts', 'action' => 'view', $accounts->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Accounts', 'action' => 'edit', $accounts->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Accounts', 'action' => 'delete', $accounts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accounts->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
