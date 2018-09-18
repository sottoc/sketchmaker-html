<?php
require '../classes/bootstrap.php';

if(!$f = empty($_GET['f']) ? false : $_GET['f'])
{
    header("HTTP/1.0 404 Not Found");
    die('No file provided!');
}

$file = realpath(APP_PATH.DIRECTORY_SEPARATOR.'media_bin'.DIRECTORY_SEPARATOR.$f);
// check if this file from user directory
if($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR']  )
{
    $userDir = $user->getUserDir();
    if(strpos($file,$userDir)!==0)
    {
        header("HTTP/1.0 403 Forbidden");
        die('You do not have access to view this file!');        
    }

}
if(!file_exists($file))
{
    Sart\FileHelper::load_file(APP_PATH.'/img/corrupted-file.png');
}else{
    Sart\FileHelper::load_file($file);
}
