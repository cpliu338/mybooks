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
