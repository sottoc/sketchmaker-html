<?php
require '../classes/bootstrap.php';

$cur_time = gmdate('U');

$timestamp = isset($_GET['t']) ? (int) $_GET['t'] : $cur_time ;

if($cur_time - $timestamp > 600)
{
    $timestamp = $cur_time;
}

$result = [];
//$timestamp = 0;
try{
    
    $messages = \Sart\SysNotification::getNotificationsForOwner($user->id,$timestamp);
    foreach($messages as $message)
    {
        $result[] = [
            'type'=>$message->type,
            'meta'=>unserialize($message->meta),
            'time'=>$message->created
        ];
    }
    echo json_encode($result);
}catch(\Exception $e)
{
    echo json_encode(['error'=>$e->getMessage()]);
}


