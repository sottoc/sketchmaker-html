<?php
define('__SKIP_AUTH__',1);

require '../classes/bootstrap.php';
ob_start();

$debug = ob_get_clean();
if(!$user = \Sart\User::model()->findByAttributes(['reset_key'=>$_POST['reset_key']]))
{
    echo json_encode([
      'error' => 'Invalid reset key!',
      'debug' => $debug
    ]);
}else{
    
    list($key,$stamp) = explode('.',$user->reset_key);
    if(time() - $stamp > 86400)
    {
        echo json_encode([
            'error'=> 'Reset key has been expired. Please use this link to get new one: <a href="/forgot.php"><b>Forgot Password</b></a>'
        ]);
        exit();
    }
    
    if($_POST['password']!= $_POST['confirmPassword'])
    {
        echo json_encode([
            'error'=> 'Password and Password Confirmation do not match!'
        ]);
        exit();        
    }
    
    $user->password = md5($_POST['password']);
    $user->reset_key = '';
    $user->save();
    echo json_encode([
        'status'=>'ok'
    ]);
}