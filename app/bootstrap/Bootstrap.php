<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 14:31
 */

class Bootstrap extends Yaf_Bootstrap_Abstract {

	public function _initConfig(Yaf_Dispatcher $dispatcher){
		$dispatcher->autoRender(FALSE);
		Yaf_Registry::set('_config', Yaf_Application::app()->getConfig());
	}

}