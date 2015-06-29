<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 15:04
 */

class ErrorController extends Yaf_Controller_Abstract {

    private $exception;

    protected function init(){

    }

    public function errorAction($exception){
        $this->exception    = $exception;
        $code               = $exception->getCode();
        $msg                = $exception->getMessage();
        print_r(array(
            'code'  => $code,
            'msg'   => $msg
        ));
    }

}