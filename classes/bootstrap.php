<?php
set_include_path(get_include_path().':'.dirname(__FILE__));
require dirname(__FILE__).'/../vendor/autoload.php';
require_once 'functions.php';


//Set APP root path
defined('APP_PATH') || define('APP_PATH',realpath(dirname(__FILE__).'/../'));

//Session start
session_start();

//Init user
$db = new spir1donov\Database();
if(php_sapi_name() != "cli")
{
    $auth = new spir1donov\Authentication($db->getInstance());
    $user = $auth->getUser();
    
    // Authenticate user only if script had runned not from server side
    if($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR'] && !defined('__SKIP_AUTH__') )
    {
    
        //Check if user is logged in    
        $user = $auth->authenticate();
        
        if (!isset($user->id)) {
          header('Location: /login.php');
        }
    }
}