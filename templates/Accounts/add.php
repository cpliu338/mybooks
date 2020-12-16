<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>
<div class="row">
    <div class="column-responsive column-80">
        <div class="accounts form content">
            <?= $this->Form->create($account) ?>
            <fieldset>
                <legend><?= __('Add Account') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('parent', ['label'=>__('Parent')]);
                    echo $this->Form->control('parent_id', ['type'=>'hidden']);
                    echo $this->Form->control('db_label');
                    echo $this->Form->control('cr_label');
                    echo $this->Form->control('currency');
                    echo $this->Form->control('tags._ids', ['options' => $tags,
                    		'multiple'=>'checkbox']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<script>
  $( function() {
    $( "#parent" ).autocomplete({
      source: "/accounts/suggest",
      minLength: 1,
      select: function( event, ui) {
      	  /*ui = ui.result;
      	  ui.content = ui.content[0];
      	  console.log(ui.item);*/
      	  $(this).attr('title', ui.item.value);
      	  $("#parent-id").val(ui.item.id);
      	  /*
      	  return ui;
      	  */
      }
    });
  } );
</script>