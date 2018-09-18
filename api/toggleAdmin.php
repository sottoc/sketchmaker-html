<?php
ob_start();
require '../classes/bootstrap.php';
if(!$user->isAdmin){
    header('Location: /');
    die();
}


if($user = \Sart\User::model()->findByPk((int)$_POST['id']))
{
    $user->toggleAdmin();
    echo json_encode([
        'status'=>'ok'
    ]);    
}else{
    header('HTTP/1.1 400 BAD REQUEST');
    echo json_encode([
        'error'=>'Invalid User ID'
    ]);
}

?>