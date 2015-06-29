<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 16:15
 */

class Helper {

    // http状态码及其对应的描述
    static public function httpStatusDescription($code=0){
        $array	= array(
            100	=> 'Continue',
            101	=> 'Switching Protocols',
            102	=> 'Processing',
            200	=> 'OK',
            201	=> 'Created',
            202	=> 'Accepted',
            203	=> 'Non-Authoritative Information',
            204	=> 'No Content',
            205	=> 'Reset Content',
            206	=> 'Partial Content',
            207	=> 'Multi-Status',
            300	=> 'Multiple Choices',
            301	=> 'Moved Permanently',
            302	=> 'Found',
            303	=> 'See Other',
            304	=> 'Not Modified',
            305	=> 'Use Proxy',
            306	=> 'Switch Proxy',
            307	=> 'Temporary Redirect',
            400	=> 'Bad Request',	// 用户发出的请求无法被服务器理解，幂等操作，用于[POST,PUT,PATCH]
            401	=> 'Unauthorized',
            402	=> 'Payment Required',
            403	=> 'Forbidden',	// 服务器已经理解请求，但拒绝执行
            404	=> 'Not Found',
            405	=> 'Method Not Allowed',
            406	=> 'Not Acceptable',	// 用户请求的格式不可用，用于[GET]
            407	=> 'Proxy Authentication Required',
            408	=> 'Request Timeout',
            409	=> 'Conflict',
            410	=> 'Gone',
            411	=> 'Length Required',
            412	=> 'Precondition Failed',
            413	=> 'Request Entity Too Large',
            414	=> 'Request-URI Too Long',
            415	=> 'Unsupported Media Type',
            416	=> 'Requested Range Not Satisfiable',
            417	=> 'Expectation Failed',
            418	=> 'I\'m a teapot',
            421	=> 'There are too many connections from your internet address',
            422	=> 'Unprocessable Entity',	// 请求格式虽然正确，但有语法错误，无法响应，用于[POST,PUT,PATCH]
            423	=> 'Locked',
            424	=> 'Failed Dependency',
            425	=> 'Unordered Collection',
            426	=> 'Upgrade Required',
            449	=> 'Retry With',
            500	=> 'Internal Server Error',
            501	=> 'Not Implemented',
            502	=> 'Bad Gateway',
            503	=> 'Service Unavailable',
            504	=> 'Gateway Timeout',
            505	=> 'HTTP Version Not Supported',
            506	=> 'Variant Also Negotiates',
            507	=> 'Insufficient Storage',
            508	=> 'Bandwidth Limit Exceeded',
            509	=> 'Not Extended',
            600	=> 'Unparseable Response Headers'
        );
        if ($code == 0){
            return $array;
        }
        return !empty($array[$code]) ? $array[$code] : 'Undefined Error';
    }

    // 尝试将一个字符串解成JSON
    static public function tryToDecodeJson(&$arg){
        if (is_array($arg)){
            return TRUE;
        }
        $tmp        = json_decode($arg, TRUE);
        if (json_last_error() === JSON_ERROR_NONE){
            $arg    = $tmp;
            return TRUE;
        }
        return FALSE;
    }
}