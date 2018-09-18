<?php
/**
 * Deprecated file !
 */

set_include_path(get_include_path().':'.dirname(__FILE__).'/../classes');
require dirname(__FILE__) . '/../vendor/autoload.php';

$db = new spir1donov\Database();
$renderQueue = new spir1donov\RenderQueue($db->getInstance());
$projects = new spir1donov\Projects($db->getInstance());

$requests = $renderQueue->getRequests();

$nextRequest = array_shift($requests);

$projectId = $nextRequest['projectId'];


echo $projectId;

$renderQueue->removeRequest($projectId);


//Temporary solution to merge audio and video
$project = $projects->getProject($projectId);
$data = json_decode($project['data']);
if(!empty($data->attrs->audio))
{
    $audio = dirname(__FILE__).'/../'.$data->attrs->audio;
    if(file_exists($audio))
    {
        copy($audio,dirname(__FILE__).'/../temp/audio/'.$projectId.'.mp3');
    }
}