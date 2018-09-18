<?php
require '../classes/bootstrap.php';

use \Sart\Video;

$video = Video::model()->findByAttributes(['id'=>(int)$_POST['vid']]);

$user = \spir1donov\Authentication::getInstance()->getUser();

if($video && $video->owner == $user->id)
{
    if($video->delete())
    {
        echo json_encode(['success'=>'Video has been deleted']);
    }else{
        echo json_encode(['error'=>'Something wrong during delete of video']);
    }
}else{
    echo json_encode(['error'=>'No video with such ID']);
}
