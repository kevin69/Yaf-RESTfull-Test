<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-29
 * Time: 15:04
 */

class Db extends PDO {

    // 给select用的
    private function selectPrepare($sql, $param=array()){
        if (!is_array($param)){
            $param  = array( $param );
        }
        $prepare    = $this->prepare($sql);
        $query      = $prepare->execute(array_values($param));
        if ($query !== TRUE){
            $error  = $prepare->errorInfo();
            throw new Exception($error[2], 500);
        }
        return $prepare;
    }

    // 获取记录集
    public function select($sql, $param=array(), $type=Db::FETCH_ASSOC){
        return empty($sql) ? FALSE : $this->selectPrepare($sql, $param)->fetchAll($type);
    }

    // 获取单条记录
    public function selectOne($sql, $param=array(), $type=Db::FETCH_ASSOC){
        return empty($sql) ? FALSE : $this->selectPrepare($sql, $param)->fetch($type);
    }

    // 插入记录
    public function insert($table, $data=array()){
        if (empty($table)){
            return FALSE;
        }
        if (strpos($table, '`') === FALSE){
            $table  = "'$table'";
        }
        $marks      = implode(',', array_fill(0, count($data), '?'));
        $cols       = implode(',', array_keys($data));
        $sql        = "INSERT INTO $table ($cols) VALUES ($marks)";
        $prepare    = $this->prepare($sql);
        $query      = $prepare->excute(array_values($data));
        if ($query !== TRUE){
            $error  = $prepare->errorInfo();
            throw new Exception($error[2], 500);
        }
        return $this->lastInsertId();
    }

    // 更新记录
    public function update($table, $data, $where='', $whereParam=array(), $suffix=''){
        if (empty($table) || empty($data)){
            return FALSE;
        }
        if (strpos($table, '`') === FALSE){
            $table      = "`$table`";
        }
        if (!is_array($whereParam)){
            $whereParam = array( $whereParam );
        }
        $queryArr       = array();
        $queryParam     = array();
        foreach ($data as $k => $v){
            $queryArr[]     = "$k=?";
            $queryParam[]   = $v;
        }
        $queryParam     = array_merge($queryParam, $whereParam);
        $sql            = "UPDATE $table SET ".implode(', ', $queryArr);
        if (!empty($where)){
            $sql        .= " WHERE $where";
        }
        if (!empty($suffix)){
            $sql        .= " $suffix";
        }
        $prepare        = $this->prepare($sql);
        $query          = $prepare->execute($queryParam);
        if ($query !== TRUE){
            $error      = $prepare->errorInfo();
            throw new Exception($error[2], 500);
        }
        return $prepare->rowCount();
    }

    // 删除记录
    public function delete($table, $where='', $whereParam=array(), $suffix=''){
        if (empty($table)){
            return FALSE;
        }
        if (strpos($table, '`')){
            $table      = "`$table`";
        }
        if (!is_array($whereParam)){
            $whereParam = array( $whereParam );
        }
        $sql            = "DELETE FROM $table";
        if (!empty($where)){
            $sql        .= " WHERE $where";
        }
        if (!empty($suffix)){
            $sql        .= " $suffix";
        }
        $prepare        = $this->prepare($sql);
        $query          = $prepare->execute($whereParam);
        if ($query !== TRUE){
            $error      = $prepare->errorInfo();
            throw new Exception($error[2], 500);
        }
        return $prepare->rowCount();
    }

}