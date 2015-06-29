<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 15:26
 */

class Model {

    protected function loadDatabase($name){
        $database   = Yaf_Registry::get('_database');
        if (isset($database[$name])){
            return $database[$name];
        }
        $config     = Yaf_Registry::get('_config');
        if (isset($config->database->$name)){
            if (is_null($database)){
                $database   = array();
            }
            $dbConfig   = $config->database->$name;
            $pdoParams  = array();
            if ($dbConfig->pconnect){
                $pdoParams[Db::ATTR_PERSISTENT] = TRUE;
            }
            try {
                $conn   = new Db($dbConfig->type.':host='.$dbConfig->host.'; dbname='.$dbConfig->database, $dbConfig->username, $dbConfig->password, $pdoParams);
            }catch (PDOException $error){
                throw new Exception($error->getMessage(), 500);
            }
            $database[$name]    = $conn;
            Yaf_Registry::set('_database', $database);
            return $conn;
        }else {
            throw new Exception('Undefined database', 500);
        }

    }


}