<?php
namespace spir1donov;

use Sart\User;

class Authentication {
  /** @var \PDO $db */
  
  private static $_instance = null;
  
  private $db;

  private $user;

  private $cookieName = 'appToken';

  function __construct(\PDO $db) {
    $this->db = $db;

    return $this->authenticate();
  }

  function authenticate() {
    
    $user = new User();

    if(isset($_COOKIE[$this->cookieName]))
    {
        $user->findByAttributes('token',$_COOKIE[$this->cookieName]);    
    }
    
    $this->user = $user;
    
    return $this->user;
  }

  function login() {
    $user = User::model()->findByAttributes([
        'email'=>$_POST['email'],
        'password'=>md5($_POST['password'])
    ]);
    
    $this->user = $user;
    
    if(isset($user->id))
    {
        $token = md5(microtime(true) . implode(' ', $this->user->getAttributes()));
        $user->updateAttribute('token', $token);
        setcookie($this->cookieName, $token, 0, '/');
    }
    
    return $this->user;
  }

  function logout() {
    setcookie($this->cookieName, '');
  }

  

    public static function getInstance()
    {
        if(!self::$_instance)
        {
            self::$_instance = new Authentication(\spir1donov\Database::getDb());
        }
        
        return self::$_instance;
    }

    
  
    /**
     * return user data
     */ 
    function getUser()
    {
      return $this->user;
    }

/**
 *@todo: Refactoring required, should be moved into the user class
 */

  function addUser() {
    $userStmt = $this->db->prepare("INSERT INTO `users` (`email`, `password`, `limit`, `addedOn`,`license`) VALUES (:email, :password, :limit, NOW(),:license)");
    return $userStmt->execute([
      'email' => $_POST['email'],
      'password' => md5($_POST['password']),
      'limit' => $_POST['limit'],
      'license' => $_POST['license']
    ]);
  }

  function editUser() {
    $userStmt = $this->db->prepare("UPDATE `users` SET `email` = :email, `password` = :password, `limit` = :limit WHERE `id` = :id");
    return $userStmt->execute([
      'id' => $_POST['id'],
      'email' => $_POST['email'],
      'password' => md5($_POST['password']),
      'limit' => $_POST['limit']
    ]);
  }

  function deleteUser() {
    $userStmt = $this->db->prepare("DELETE FROM `users` WHERE `id` = :id");
    return $userStmt->execute([
      'id' => $_POST['id']
    ]);
  }

  function getUsersList() {
   
    $where = [];
    $values =[];
    if(!empty($_POST['license']))
    {
        $where[] = ' `license` LIKE :license';
        $values['license'] = '%'.$_POST['license'].'%';
    }
    if(!empty($_POST['email']))
    {
        $where[] = ' `email` LIKE :email';
        $values['email'] = '%'.$_POST['email'].'%';
    }
    $where = count($where) > 0 ? 'WHERE ' . implode(' AND ',$where) : '';
    $listStmt = $this->db->prepare("SELECT `id`, `email`, `limit`,`license`,`addedOn`, `isAdmin` FROM `users` $where");
    //var_dump("SELECT `id`, `email`, `limit`,`license`,`addedOn`, `isAdmin` FROM `users` $where");
    $listStmt->execute($values);
    return $listStmt->fetchAll();
  }


    
}