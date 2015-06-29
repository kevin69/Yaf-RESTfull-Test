<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 15:04
 */

class ErrorController extends Controller {

    private $exception;

    protected function init(){
        parent::init();
    }

    public function errorAction($exception){
        $this->exception    = $exception;
        $code               = $exception->getCode();
        switch ($code){
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
                $code   = 404;
                break;
            case YAF_ERR_STARTUP_FAILED:
            case YAF_ERR_ROUTE_FAILED:
            case YAF_ERR_DISPATCH_FAILED:
            case YAF_ERR_NOTFOUND_VIEW:
            case YAF_ERR_CALL_FAILED:
            case YAF_ERR_AUTOLOAD_FAILED:
            case YAF_ERR_TYPE_ERROR:
                $code   = 500;
                break;
        }
        if (empty($code)){
            $code   = 500;
        }
        if ($this->_request->isCli()){
            $this->_responseCliError($code);
        }else if ($this->_request->isXmlHttpRequest()){
            $this->_responseXhrError($code);
        }else {
            $this->_responsePageError($code);
        }
    }

    private function _responseCliError($code=500){
        $codeDesc   = Helper::httpStatusDescription($code);
        $msg        = $this->exception->getMessage();
        $isJson     = Helper::tryToDecodeJson($msg);
        $ret        = "$code\t$codeDesc";
        if ($isJson){
            $arr    = $msg;
            unset($arr['message']);
            unset($msg['headers']);
            $error  = $msg['message'];
        }else {
            $error  = $msg;
        }
        if (!empty($error)){
            $ret    .= "\nmessage\t$error";
        }
        if (!empty($arr)){
            foreach ($arr as $k => $v){
                $ret    .= "\n$k\t$v";
            }
        }
        echo $ret;
        exit();
    }

    private function _responseXhrError($code=500){
        $codeDesc   = Helper::httpStatusDescription($code);
        $msg        = $this->exception->getMessage();
        $protocol   = $this->_request->getServer('SERVER_PROTOCOL');
        $isJson     = Helper::tryToDecodeJson($msg);
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
                // 当$data只有message了，直接输出$msg的字符串
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
        if (in_array($code, 405) || in_array($this->_request->getMethod(), array('HEAD', 'OPTIONS'))){
            header('content-length: 0');
            exit();
        }
        if ($isJson){
            echo json_encode($data);
        }else {
            echo $msg;
        }
    }

    private function _responsePageError($code=500){
        $codeDesc   = Helper::httpStatusDescription($code);
        $msg        = $this->exception->getMessage();
        $protocol   = $this->_request->getServer('SERVER_PROTOCOL');
        $isJson     = Helper::tryToDecodeJson($msg);
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
            if (empty($data['message'])){
                $data['message']    = $codeDesc;
            }
        }else {
            $data   = array(
                'message'   => !empty($msg) ? $msg : $codeDesc
            );
        }
        if (in_array($code, array(405)) || in_array($this->_request->getMethod(), array('HEAD', 'OPTIONS'))){
            header('content-length: 0');
            exit();
        }
        $data['httpStatus'] = $code;
        $this->display($code, $data);
    }

}