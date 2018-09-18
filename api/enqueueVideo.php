<?php
require '../classes/bootstrap.php';

use \Sart\Video;
$renderQueue = new spir1donov\RenderQueue($db->getInstance());
$projects = new spir1donov\Projects($db->getInstance());


$projectId = (int)$_POST['project'];
$fileFormat = $_POST['fileFormat'];
$videoResolution = $_POST['videoResolution'];

$project = $projects->getProject($projectId);

if ($project['owner'] === $user->id) {
     
    $video = Video::model()->findByAttributes(['project'=>$project['id']]);
    if(!$video)
    {
        $video = new Video;
        $video->owner = $project['owner'];
        $video->project = $projectId;

    }
    
    if(!$video->getIsNewRecord() && $video->status !== Video::STATUS_RENDERED )
    {
        $debug = ob_get_clean();
        echo json_encode([
            'result' => $projectId,
            'debug' => $debug,
            'error'=>'You already has video for project &laquo; '.$project['name'].'&raquo in queue for rendering! Please wait until it will be finished before add new one!'
        ]);
        die();
    }
    
    $video->status = Video::STATUS_IN_QUEUE;
    $video->progress = 0;
    $video->fileFormat = $fileFormat;
    $video->videoResolution = $videoResolution;
    $video->save();
    
    $result = $renderQueue->addRequest($user->id, $projectId, $fileFormat, $videoResolution,$video->id,$project['duration']);

  $debug = ob_get_clean();
  echo json_encode([
    'result' => $projectId,
    'debug' => $debug,
    'sql_res'=>$result
]);
}
