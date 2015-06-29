<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 14:54
 */

define('APP_PATH', realpath(dirname(__FILE__).'/').'/app/');
$app    = new Yaf_Application(APP_PATH.'app.ini', '');
$app->bootstrap()
    ->run();