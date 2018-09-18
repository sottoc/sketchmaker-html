<?php
define('__SKIP_AUTH__',1);
require_once dirname(__FILE__) . '/../classes/bootstrap.php';
        
$request =  \Sart\Request::model()->findByAttributes(['id'=>(int)$_POST['requestId']]);        
if($request)
{
    $frames = (int)$_POST['frames'];
    $request->renderedFrames = $frames;
    $request->calculateProgress();
   
    if($request->save())
    {
        \Sart\SysNotification::addNotify(\Sart\SysNotification::TYPE_UPDATE_PROGRESS,$request->ownerId ,['video'=>$request->videoId,'progress'=>$request->progress]);    
    }
    
}