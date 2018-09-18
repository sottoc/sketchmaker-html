<?php
define('__SKIP_AUTH__',1);

require '../classes/bootstrap.php';
ob_start();
$debug = ob_get_clean();
if(!$user = \Sart\User::model()->findByAttributes(['email'=>$_POST['email']]))
{
    echo json_encode([
      'error' => 'No Account with such Email is registered here - Please Verify',
      'debug' => $debug
    ]);
}else{
    $user->reset_key = randomString(32).'.'.time();
    $user->save();
    $mailer = new \Sart\Mailer();
    if($mailer->emailPasswordReset($user))
    {
        echo json_encode([
            'status'=>'ok'
        ]);
    }else{
        echo json_encode([
            'error'=>$mailer->error
        ]); 
    }    
}

/*
 * Create a random string
 * @author	XEWeb <>
 * @param $length the length of the string to create
 * @return $str the string
 */
function randomString($length = 6) {
	$str = "";
	$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
}