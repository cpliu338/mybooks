<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;
use Cake\Core\Configure;
use Cake\Controller\Controller;
use Cake\Routing\Router;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Session');
        $this->loadComponent('Authentication.Authentication');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
        $menuitems = [
        	['href'=>['controller'=>'Accounts', 'action'=>'index'], 'innerHtml'=>__('My Books'), 'id'=>'accounts-link'],
        	['href'=>['controller'=>'Users', 'action'=>'index'], 'innerHtml'=>__('Users'), 'id'=>'users-link'],
        	['href'=>['controller'=>'Tags', 'action'=>'index'], 'innerHtml'=>__('Tags'), 'id'=>'tags-link'],
        	['href'=>['controller'=>'Users', 'action'=>'logout'], 'innerHtml'=>__('Log out'), 'id'=>'logout-link'],
        	['href'=>['controller'=>'Commodities', 'action'=>'index'], 'innerHtml'=>__('Commodities'), 'id'=>'commodities-link'],
        	['href'=>'#', 'innerHtml'=>__('Settings'), 'id'=>'link-settings']
		];
		$env = Configure::read('env', '');
		$this->set(compact('menuitems', 'env'));
    }
    
    protected function setupTagFilter() {
    	$tagfilter = explode(',', $this->Session->get('tagFilter'));
        $tags = $this->Accounts->Tags->find('list');
        $this->set(compact('tags', 'tagfilter'));
        //return $tagfilter;
    }
}
