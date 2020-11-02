<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;
use Cake\I18n\FrozenDate;
/**
 * Settings cell
 */
class SettingsCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Initialization logic run at the end of object construction.
     *
     * @return void
     */
    public function initialize(): void
    {
    	$this->loadComponent('Session');
    }

    /**
     * Default display method.
     *
     * @return void
     */
    public function display()
    {
    	/*
    	$saved = $this->request->getSession()->read('Transaction.bfDate');
    	$this->set('bfDate', $saved ? 
    		new FrozenDate($saved) :
    		FrozenDate::now()->subDays(100));
    		*/
    	$this->set('bfDate', $this->Session->get('Transaction.bfDate'));
    }
}
