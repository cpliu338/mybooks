<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account[]|\Cake\Collection\CollectionInterface $accounts
 */
?>
<div>
<ul id="toggleable">
<li data-id="0">Any</li>
<?php foreach ($tags as $id=>$name): ?>
<li data-id="<?= $id?>"> <?= $name?></li>
<?php endforeach; ?>
</ul>
<button id="reload">Reload</button>
<span id="result"></span>
</div>
<div class="accounts index content">
    <?= $this->Html->link(__('New Account'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Accounts') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('code') ?></th>
                    <th><?= $this->Paginator->sort('db_label') ?></th>
                    <th><?= $this->Paginator->sort('cr_label') ?></th>
                    <th><?= $this->Paginator->sort('currency') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?= $this->Number->format($account->id) ?></td>
                    <td><?= h($account->name) ?></td>
                    <td><?= h($account->code) ?></td>
                    <td><?= h($account->db_label) ?></td>
                    <td><?= h($account->cr_label) ?></td>
                    <td><?= h($account->currency) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $account->id]) ?>
                        <?= $this->Html->link(__('Summary'), ['action' => 'summary-view', $account->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $account->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $account->id], ['confirm' => __('Are you sure you want to delete # {0}?', $account->id)]) ?>
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
<script>
var selected = [<?php echo join($tagfilter, ',')?>];
$(function () {
	$("#toggleable li").each(function (index){
		if (selected.includes($(this).data('id'))) 
			$(this).addClass("ui-selected");
	});
	$("#reload").click(function() {
			/*alert($('meta[name="csrfToken"]').attr('content'));
			*/
		$.ajax({
			url: "<?=$this->url->build(['action'=>"setFilter"])?>",
			dataType: 'json',
			contentType: 'application/json; charset=UTF-8',
			data: JSON.stringify(selected),
			headers: {/*Accept: "application/json",*/
			"X-CSRF-Token": $('meta[name="csrfToken"]').attr('content')},
		  	method: 'post'
		}).success(function (content) {
			location.reload();
		}).error(function (jqXHR, textStatus, errorThrown) {
			$("#result").html(JSON.stringify(errorThrown));
		});
	});
	$("#toggleable li").click(function() {
		found = false;
		for (var i = 0; i<selected.length && !found; i++) {
			if (selected[i] === $(this).data('id')) {
				selected.splice(i, 1);
				found = true;
			}
		}
		if (!found) selected.push($(this).data('id'));
		$(this).toggleClass("ui-selected");
	});
});
</script>