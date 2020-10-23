<?php
declare(strict_types=1);

namespace App\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

/**
 * Entries shell command.
 */
class EntriesShell extends Shell
{
    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->out($this->OptionParser->help());
    }
    
    public function nextCode($code1) {
    	$this->loadModel('Accounts');
    	$this->out($this->Accounts->findNextChildCode($code1));
    }
    
    public function create() {
    	$this->loadModel('Transactions');
    	$this->loadModel('Entries');
    	$tran  = $this->Transactions->newEmptyEntity();
    	$tran->date1 = '2020-08-01';
    	$tran->description = 'opening';
    	$en = $this->Entries->newEmptyEntity();
    	$en->transaction = $tran;
    	$en->account_id = 1;
    	$en->status = 'n';
    	$en->real_amount = 100;
    	$en->home_amount = 100;
    	$this->Entries->save($en);
        $this->out("Created " . $en);
    }
    
}
