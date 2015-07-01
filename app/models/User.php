<?php
/**
 * Created by PhpStorm.
 * User: goaway
 * Date: 15-6-30
 * Time: 9:55
 */

class UserModel extends Model {

    private $db;
    private $groups = array(
        1   => '管理员',
        2   => '普通用户'
    );

    function __construct(){
        $this->db   = $this->loadDatabase('main');
    }

    public function loadUserById($uid){
        return $this->db->selectOne('SELECT * FROM `users` WHERE uid=?', array( $uid ));
    }

    public function loadUserByName($username){
        return $this->db->selectOne('SELECT * FROM `users` WHERE username=?', array( $username ));
    }

    public function loadUserByEmail($email){
        return $this->db->selectOne('SELECT * FROM `users` WHERE email=?', array( $email ));
    }

    public function loadGroups(){
        return $this->groups;
    }

    public function createPassword($password){
        return md5($password);
    }

    public function checkPassword($password, $userInfo){
        return md5($password) === $userInfo['password'];
    }

    public function createUser($param){
        return $this->db->insert('users', $param);
    }

    public function updateUser($uid, $param){
        return $this->db->update('users', $param, 'uid=?', $uid);
    }

}