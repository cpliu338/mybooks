<div class="row">
    <div class="column-responsive column-80">
        <div class="entries form content">
            <?= $this->Form->create($transaction) ?>
            <fieldset>
                <legend><?= __('Earn Interest') ?></legend>
                <?php
                    echo $this->Form->control('date1');
                    echo $this->Form->control('description');
                    echo $this->Form->control('entry1_accountid', [
                    	'options' => $account1_choices,
						'value'=> $entry1->account_id
                    	]);
                    echo $this->Form->control('entry1_homeamount', ['type'=>'number', 'step'=>'0.01']);
                    echo $this->Form->control('entry2_accountid', [
                    	'options' => $account2_choices,
						'value'=> $entry2->account_id
                    	]);
                    echo $this->Form->control('entry2_homeamount', ['type'=>'number', 'step'=>'0.01']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
