<?php
define('__SKIP_AUTH__',1);
require_once dirname(__FILE__) . '/../classes/bootstrap.php';


$renderQueue = new spir1donov\RenderQueue($db->getInstance());

$projects = new spir1donov\Projects($db->getInstance());

$requests = $renderQueue->getRequests();


$processingRequests = $renderQueue->getProcessingRequests(true);

//Check, if we have requests in queue
if (is_array($requests) && ($rCount = count($requests)) > 0) {
    //If we have free renderers
    if($freeR = $renderQueue->getFreeRenderer() )
    {
        // then start render process for all available renderers
        for($i=0; $i < $freeR; $i++)
        {
            $projectId = $requests[$i]['projectId'];
            try{
                if(!$renderQueue->doRender($requests[$i]))
                {
                    echo $renderQueue->error."\n";
                }else{
                    echo date('Y-m-d H:i:s').' renderer has been run for project  '.$projectId."\n";
                }
                
            }catch(\Exception $e)
            {
                die('Error during start renderer: '.$e->getMessage()."\n");
            }
            

            $project = $projects->getProject($projectId);            
            $data = json_decode($project['data']);
            
            $project_dir = dirname(__FILE__).'/../temp/audio/'.$projectId.'_'.$requests[$i]['id'];
            //check if we have background audio attached to project
            if(!empty($data->attrs->audio))
            {
                $audio = dirname(__FILE__).'/../'.ltrim(str_replace('/storage/','/media_bin/', $data->attrs->audio),'/');
                if(file_exists($audio))
                {
                    \Sart\FileHelper::createDirIfNotExist($project_dir);                    
                    copy($audio,$project_dir.'/background.mp3');
                }
            }
            
            //check if we have audios attached to itemss
            $audios = $projects->getAudioMap($data);
            if(!empty($audios))
            {
                \Sart\FileHelper::createDirIfNotExist($project_dir);
                $owner = new \Sart\User;
                $owner->id = $project['owner'];

                $audio_dir = $owner->getUserDir().DIRECTORY_SEPARATOR.'projects'.DIRECTORY_SEPARATOR.$projectId.DIRECTORY_SEPARATOR.'mp3'.DIRECTORY_SEPARATOR;
                
                foreach($audios as $audio)
                {
                    $file =  $audio_dir.$audio['file'].'.mp3';
                    if(file_exists($file))
                        copy($file,$project_dir.'/'.$audio['time'] * 1000 .'_'. $audio['file'].'.mp3');
                }
            }
            
            //Stop cycle if no more requests in queue
            if($rCount == $i+1 )
            {
                break;
            }
        }
    }

}else{
    //No requests. Let's check if we have somethign in process    
    //If nothing in Process, then exit
    if (empty($processingRequests)) {
        //die( date('Y-m-d H:i:s') .' - Nothing to do. Exit.');
    }
}

//Let's check if something got finished already
$captured = $renderQueue->getCaptured();
//For each captured file we need to start convertation
foreach($captured as $file)
{
    echo date('Y-m-d H:i:s').' Captured file found for project ID  '. $file['projectId']."\n";
    

    if(!isset($processingRequests[$file['requestId']]))
    {
        echo date('Y-m-d H:i:s').' ERROR: can\'t find processing request for finished capturing '.$file['pathname'].'.'."\n";
        continue;
    }

    echo date('Y-m-d H:i:s').' Start convertation'."\n";
    
    
    if(! $renderQueue->doConvertation($file, $processingRequests[$file['requestId']]))
    {
        echo date('Y-m-d H:i:s') . ' '. $renderQueue->error."\n";
        continue;
    }
}

//Let's check if something already converted
$converted = $renderQueue->getConverted();
//For each converted file we need to finalize process
foreach($converted as $file)
{
    echo date('Y-m-d H:i:s').' Converted file found for project ID  '. $file['projectId']."\n";
    
    if(!isset($processingRequests[$file['requestId']]))
    {
        echo date('Y-m-d H:i:s').' ERROR: can\'t find processing request for converted '.$file['pathname'].'.'."\n";
        continue;
    }

    echo date('Y-m-d H:i:s').' Start finalization for '.$file['pathname']."\n";
    
    $project = $projects->getProject($processingRequests[$file['requestId']]['projectId']);
    if(! $renderQueue->doFinalize($file,$processingRequests[$file['requestId']],$project))
    {
        echo date('Y-m-d H:i:s') . ' '. $renderQueue->error."\n";
    }
}
