<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 14:58
 */

class Bootstrap extends Yaf_Bootstrap_Abstract {

    // 把一些配置信息放进_config中，并关闭了自动渲染模板
    public function _initConfig(Yaf_Dispatcher $dispatcher){
        $dispatcher->autoRender(FALSE);
        Yaf_Registry::set('_config', Yaf_Application::app()->getConfig());
    }

    // 加载本地library
    public function _initLoader(){
        $loader = Yaf_loader::getInstance();
        $loader->registerLocalNamespace(array(
            'Db', 'Model', 'View', 'Controller', 'Helper'
        ));
        //$loader->import(APP_PATH.'helpers/function.php');
    }

    // 注册模板引擎
    public function _initView(Yaf_Dispatcher $dispatcher){
        $myView = new View(NULL);
        $dispatcher->setView($myView);
    }

}