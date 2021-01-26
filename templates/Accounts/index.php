<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account[]|\Cake\Collection\CollectionInterface $accounts
 */
?>
<div>
<?= $this->element('tag_chooser', ['ajax'=>false])?>
</div>
<div class="accounts index content">
    <?= $this->Html->link(__('New Account'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Accounts') ?>
    </h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('name') ?>
                        <input placeholder="name contains" id="name-filter"
                        value="<?=$nameFilter?>">
					</th>
                    <th><?= $this->Paginator->sort('code') ?></th>
                    <th><?= __('Balance') ?></th>
                    <th><?= $this->Paginator->sort('currency') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?= h($account->name) ?></td>
                    <td><?= h($account->code) ?></td>
                    <td data-accid="<?=$account->id?>" class="autoload" style="text-align:right; font-family:Courier New">
                    	<?= __('Loading') . '...'?>
					</td>
                    <td><?= h($account->currency) ?></td>
                    <td class="actions">
                        <?= $this->Html->link('', ['action' => 'view', $account->id], ['class'=>'fa fa-file-o', 'title'=>__('View')]) ?>
                        <?= $this->Html->link('', ['action' => 'summary-view', $account->id], ['class'=>'fa fa-files-o', 'title'=>__('Summary')]) ?>
                        <?= $this->Html->link('', ['action' => 'edit', $account->id], ['class'=>'fa fa-pencil', 'title'=>__('Edit')]) ?>
                        <?php // $this->Form->postLink(__('Delete'), ['action' => 'delete', $account->id], ['confirm' => __('Are you sure you want to delete # {0}?', $account->id)]) ?>
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
//var selected = [<?php echo join($tagfilter, ',')?>];
$(function () {
	$(".autoload").each(function () {
		element = $(this);
		$.ajax({
			url: "<?=$this->url->build(['action'=>'checkBalance'])?>/" + $(this).data('accid'),
			dataType: 'json',
			contentType: 'application/json; charset=UTF-8',
			headers: {
			"X-CSRF-Token": $('meta[name="csrfToken"]').attr('content')},
		  	method: 'get'
		}).success(function (content) {
			//console.log(content.balance);
			$("td[data-accid="+content.account_id+"]").html(content.balance);
		}).error(function (jqXHR, textStatus, errorThrown) {
			element.html(JSON.stringify(errorThrown));
		});
	});
	$("#name-filter").change(function () {
		value = $("#name-filter").val();
		window.location = '<?=$this->url->build([
			'controller'=>'Accounts','action'=>'index'
				])?>'+
				`?nameFilter=${encodeURIComponent(value)}`;
		window.reload();
	});
});
</script>