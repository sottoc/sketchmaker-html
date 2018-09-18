<?php
ob_start();
require '../classes/bootstrap.php';

if($user = \Sart\User::model()->findByPk((int)$_POST['id']))
{
    $mailer = new \Sart\Mailer();
    if($mailer->emailLicense($user))
    {
        echo json_encode([
            'status'=>'ok'
        ]);
    }else{
        header('HTTP/1.1 400 BAD REQUEST');
        echo json_encode([
            'error'=>$mailer->error
        ]); 
    }
}else{
    header('HTTP/1.1 400 BAD REQUEST');
    echo json_encode([
        'error'=>'Invalid User ID'
    ]);
}

?>