<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 15:38
 */

class View extends Yaf_View_Simple {

    private $fileType;

    public function display($tpl, $tpl_var=array()){
        return parent::display($this->getTpl($tpl), $tpl_var);
    }

    public function render($tpl, $tpl_var=array()){
        return parent::render($this->getTpl($tpl), $tpl_var);
    }

    private function getTpl($tpl){
        $backTract      = debug_backtrace();
        $isViewCalled   = FALSE;
        foreach ($backTract as $v){
            if ($v['class'] === 'Yaf_View_Simple'){
                $isViewCalled   = TRUE;
                break;
            }
        }
        $config         = Yaf_Application::app()->getConfig();
        $dispatcher     = Yaf_Dispathcer::getInstance();
        $this->fileType = $config->application->view->ext;
        $request        = $dispatcher->getRequest();
        $module         = strtolower($request->module);
        $controller     = strtolower($request->controller);
        $searchSlash    = strpos($tpl, '//');
        if (strpos($tpl, '.'.$this->fileType) === FALSE){
            // view里的display|render的时候，后缀名给丢了，这边给补回来
            $tpl        .= '.'.$this->fileType;
        }
        if ($searchSlash !== FALES){
            // 出现这种情况的时候表示，调用display|render的时候用的是/作为开头的，模板的查找路径顺序为
            // /modules/{modules}/views/
            // /views/
            $tpl        = substr($tpl, $searchSlash+2);
            $pathArray  = array(
                APP_PATH.'modules/'.$module.'/views/',
                APP_PATH.'views/'
            );
            $path       = $this->returnExistFile($pathArray, $tpl);
            if ($path === FALSE){
                throw new Exception('Failed opening template', YAF_ERR_NOTFOUND_VIEW);
            }
            $this->setScriptPath($path);
            return $tpl;
        }else {
            // 调用时不是用/为开头，这边在view里调用和在controller里调用是有所不同的，view调用的时候没有了所在的controller名称，这种情况下查找路径顺序为
            // /modules/{modules}/views/{controller}/
            // /views/{controller}/
            // /views/
            if ($isViewCalled !== TRUE){
                // 把{controller}/脱出
                $controllerPath = $controller.'/';
                $searchPath     = strpos($tpl, $controllerPath);
                if ($searchPath === 0){
                    $tpl        = substr($tpl, strlen($controllerPath));
                }
            }
            $pathArray          = array(
                APP_PATH.'modules/'.$module.'/views/'.$controller.'/',
                APP_PATH.'views/'.$controller.'/',
                APP_PATH.'views/'
            );
            $path               = $this->returnExistsFile($pathArray, $tpl, $controller);
            if ($path === FALSE){
                throw new Exception('Failed opening template', YAF_ERR_NOTFOUND_VIEW);
            }
            $this->setScriptPath($path);
            return $tpl;
        }
    }

    private function returnExistsFile($pathArray, &$tpl, $controller=''){
        foreach ($pathArray as $v){
            if (file_exists($v.$tpl)){
                return $v;
            }
        }
        // 处理success和error的特殊模板，比如404找不到的时候，就找4XX
        $tmp        = empty($controller) ? $tpl : $controller.'/'.$tpl;
        if (preg_match('/\/?(error|success)\/([1-6]\d{2})\.'.$this->fileType.'$/', $tmp, $matches)){
            $tpl    = preg_replace('/([1-6])\d{2}(\.'.$this->fileType.')$/', '${1}XX${2}', $tpl);
            foreach ($pathArray as $v){
                if (file_exists($v.$tpl)){
                    return $v;
                }
            }
        }
        return FALSE;
    }

}