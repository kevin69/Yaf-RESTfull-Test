<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 15:00
 */

class IndexController extends Controller {

    protected function init(){
        parent::init();
    }

    public function indexGetAction(){
        echo 'Hello world';
    }

    public function indexPostAction(){
        echo 'Hello world Post';
    }

    public function testGetAction(){
        $this->responseSuccess('Hello world');
    }

}