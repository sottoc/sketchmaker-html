<?php
namespace spir1donov;

use \PDO;

class Database {
  private static $_handle = null;

  public function __construct() {
    require dirname(__FILE__) . '/db_settings.php';

    self::$_handle = new PDO("mysql:host={$dbSettings['hostname']};dbname={$dbSettings['dbname']};charset=utf8", $dbSettings['username'], $dbSettings['password'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    ]);
  }

  public function getInstance() {
    return self::$_handle;
  }
  
    /**
     * Get Db connection
     */
    static function getDb(){
        if(empty(self::$_handle))
        {
            self::$_handle = new Database();
        }
        
        return self::$_handle;
    }
  
}