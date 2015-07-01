<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 16:19
 */

class Controller extends Yaf_Controller_Abstract {

    protected $_config;
    protected $_request;
    private $stdinData;

    protected function init(){
        $this->_config  = Yaf_Registry::get('_config')->toArray();
        $this->_request = $this->getRequest();
        $this->getView()->assign(array(
            '_site'     => $this->_config['site']
        ));
        // 开始把框架改造成RESTful，并自动加上OPTIONS的返回
        $method         = $this->_request->getMethod();
        $action         = $this->_request->getActionName();
        if (method_exists($this, $action.'Action')){
            // 兼容原方法，如果已经存在xxxAction的时候直接调用，不再进行RESTful，否则ErrorController与forward的时候会出错
            return;
        }
        $allow          = array();
        foreach (array('Get', 'Post', 'Put', 'Delete', 'Head', 'Patch') as $v){
            if (method_exists($this, $action.$v.'Action')){
                $allow[]    = $v;
            }
        }
        if (empty($allow)){
            throw new Exception('', 404);
        }
        if ($method == 'OPTIONS'){
            header('allow: '.strtoupper(implode(' ', $allow)));
            header('content-length: 0');
            exit();
        }else {
            $camelMethod    = substr($method, 0, 1).strtolower(substr($method, 1));
            if (!in_array($camelMethod, $allow)){
                throw new Exception('', 405);
            }
            // 重新定位到RESTful对应的action上，比如POST方式请求index，则从indexAction变成indexPostAction
            $this->_request->setActionName($action.$camelMethod);
        }
    }

    protected function getPutData($name, $defaultValue=NULL){
        if (empty($this->stdinData)){
            $this->stdinData    = $this->_getData();
        }
        $data   = $this->stdinData;
        return isset($data[$name]) ? $data[$name] : $defaultValue;
    }

    protected function getPatchData($name, $defaultValue=NULL){
        return $this->getPutData($name, $defaultValue);
    }

    private function _getData(){
        parse_str(file_get_contents('php://stdin'), $data);
        return $data;
    }

    // 在控制器中用这个方法来简单地throw error
    protected function throwError($msg='', $code=400){
        throw new Exception($msg, $code);
    }

    // 返回成功的请求，msg可以为字符串或者是json数组
    protected function responseSuccess($msg='', $code=200){
        if ($this->_request->isCli()){
            $this->_responseCliSuccess($msg, $code);
        }else if ($this->_request->isXmlHttpRequest()){
            $this->_responseXhrSuccess($msg, $code);
        }else {
            $this->_responsePageSuccess($msg, $code);
        }
    }

    private function _responseCliSuccess($msg='', $code=200){
        $codeDesc   = Helper::httpStatusDescription($code);
        $isJson     = Helper::tryToDecodeJson($msg);
        $ret        = "$code\t$codeDesc";
        if ($isJson){
            $arr    = $msg;
            unset($arr['message']);
            unset($msg['headers']);
            $success    = $msg['message'];
        }else {
            $success    = $msg;
        }
        if (!empty($success)){
            $ret    .= "\nmessage\t$success";
        }
        if (!empty($arr)){
            foreach ($arr as $k => $v){
                $ret    .= "\n$k\t$v";
            }
        }
        echo $ret;
        exit();
    }

    private function _responseXhrSuccess($msg='', $code=200){
        $codeDesc   = Helper::httpStatusDescription($code);
        $isJson     = Helper::tryToDecodeJson($msg);
        $protocol   = $this->_request->getServer('SERVER_PROTOCOL');
        header("$protocol $code $codeDesc");
        header('charset: utf-8');
        if ($isJson){
            $data   = $msg;
            unset($data['headers']);
            if (!empty($msg['headers'])){
                foreach ($msg['headers'] as $k => $v){
                    if (strtolower($k) == 'location'){
                        $k  = 'redirect';
                    }
                    header("$k: $v");
                }
            }
            $count  = count($data);
            if ($count == 1 && isset($data['message'])){
                // 当$data只剩下message，直接输出$msg字符串
                header('content-type: text/plain');
                $msg    = $data['message'];
                $isJson = FALSE;
            }else if ($count == 0){
                // 连message都没有，没有输出
                header('content-type: text/plain');
                header('content-length: 0');
                exit();
            }else {
                header('content-type: application/json');
            }
        }
        if (in_array($code, array(204)) || in_array($this->_request->getMethod(), array('HEAD', 'OPTIONS'))){
            header('content-length: 0');
            exit();
        }
        if ($isJson){
            echo json_encode($data);
        }else {
            echo $msg;
        }
    }

    private function _responsePageSuccess($msg='', $code=200){
        $codeDesc   = Helper::httpStatusDescription($code);
        $isJson     = Helper::tryToDecodeJson($msg);
        $protocol   = $this->_request->getServer('SERVER_PROTOCOL');
        header("$protocol $code $codeDesc");
        header('charset: utf-8');
        if ($isJson){
            $data   = $msg;
            unset($data['headers']);
            if (!empty($msg['headers'])){
                foreach ($msg['headers'] as $k => $v){
                    if (strtolower($k) == 'location'){
                        $data['redirect']   = $v;
                        continue;
                    }
                    header("$k: $v");
                }
            }
        }else {
            $data   = array(
                'message'   => !empty($msg) ? $msg : NULL
            );
        }
        if (in_array($code, array(204)) || in_array($this->_request->getMethod(), array('HEAD', 'OPTIONS'))){
            header('content-length: 0');
            exit();
        }
        $data['httpStatus'] = $code;
        $this->display("/success/$code", $data);
    }

}