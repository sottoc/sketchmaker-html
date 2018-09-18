<?php
namespace spir1donov;

require_once dirname(__FILE__) . '/render.config.inc.php';

use \PDO;

class RenderQueue {
    /** @var \PDO $db */
    private $db;
    
    //private $requests;
    //private $processingRequests;
    //private $tempDir;
    //private $rendererLock;
    //private $converterLock;

    
    public $error = '';
    
    /**
     * Consructor. Init db
     */
    public function __construct(\PDO $db) {
        $this->db = $db;
      
        //$this->tempDir = dirname(__FILE__) . '/../temp/';
        //$this->rendererLock = $this->tempDir . 'renderer.lock';
        //$this->converterLock = $this->tempDir . 'converter.lock';
    }

    /**
     * Add request to queue
     */
    public function addRequest($ownerId, $projectId, $fileFormat, $videoResolution,$videoId,$duration = 0) {
        $stmt = $this->db->prepare("INSERT INTO `requests` (`ownerId`, `projectId`, `fileFormat`, `videoResolution`,`videoId`,`totalFrames`,`progress`) VALUES (:ownerId, :projectId, :fileFormat, :videoResolution, :videoId,:totalFrames,1)");
        return $stmt->execute([
          'ownerId' => $ownerId,
          'projectId' => $projectId,
          'fileFormat' => $fileFormat,
          'videoResolution' => $videoResolution,
          'videoId'=> $videoId,
          'totalFrames'=>ceil($duration * RENDER_FPS)
        ]);
    }

    /**
     * Add record for processign into db
     */
    public function addProcessingRequest($projectId,$requestId,$displayNum,$pid) {
        $stmt = $this->db->prepare("INSERT INTO `processingRequests` (`projectId`,`requestId`,`display`,`pid`,`status`) VALUES (:projectId, :requestId, :display, :pid, :status)");
    
        $result = $stmt->execute([
          'projectId' => $projectId,
          'requestId' => $requestId,
          'display' => $displayNum,
          'pid' => $pid,
          'status' => 'rendering'
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Get requests from render queue that waiting for rendering
     * @return array|null
     */
    public function getRequests() {
        $stmt = $this->db->prepare("SELECT `ownerId`, `projectId`, `id` FROM `requests` WHERE `inProcess` <> 1");
        $stmt->execute();
      
        $this->requests = $stmt->fetchAll();
      
        return $this->requests;
    }

    
    /**
     * Return requests in process
     */
    public function getProcessingRequests($indexRequestId = false) {
        $stmt = $this->db->prepare("SELECT * FROM `processingRequests`");
        $stmt->execute();
      
        $this->processingRequests = $stmt->fetchAll();
      
        if($indexRequestId)
        {
            $rows = [];
            foreach((array)$this->processingRequests as $row)
            {
                $rows[$row['requestId']] = $row;
            }
            return $rows;
        }
      
        return $this->processingRequests;
    }

    /**
     * Update status for processing request
     * @param int $processingId
     * @param string $status
     */
    public function updateProcessingStatus($processingId,$status)
    {
         $stmt = $this->db->prepare("UPDATE processingRequests SET status = :status WHERE id=:id");
    
        return $stmt->execute([
          'status' => $status,
          'id' => $processingId,
        ]);        
    }
    
    /**
     * Add inProcess sign to request
     * @param int $requestId
     */
    public function setRequestInProcess($requestId){
        $stmt = $this->db->prepare("UPDATE requests, videos
        SET
            requests.inProcess = :inProcess,
            videos.status = :status,
            videos.updated = NOW()
            WHERE requests.id=:id
            AND videos.id  = requests.videoId
        ");
        
        return $stmt->execute([
          'inProcess' => 1,
          'id' => $requestId,
          'status' =>\Sart\Video::STATUS_IN_PROCESS
        ]);
    }
    
    
    /**
     *  Start render process
     *  @param array $request - array with request info
     */
    public function doRender($request)
    {
        //check if we have free display 
        if(!$displayNum = self::getFreeDisplayNum())
        {
            throw new \Exception('No free displays for rendering!');
        }
        
        
        //Clear chromium-browser  profile
        $profile_dir = RENDER_PROFILES_DIR.'profile_'.$displayNum;
        if(is_dir($profile_dir))
        {
            system('rm -rf ' . escapeshellarg($profile_dir), $retval);
        };
        
        $output = [];
        //Run Xvfb
        passthru("Xvfb :$displayNum -screen 0 '1920x1200x24' -ac &> /dev/null &", $output);
        passthru('DISPLAY=:'.$displayNum.' chromium-browser --user-data-dir='.$profile_dir.'  --no-sandbox -start-maximized "'.RENDER_ENTRY_POINT_URL.'server-side/video-download.php?project='.$request['projectId'].'&requestId='.$request['id'].'" > /dev/null 2>/dev/null &');

        //Get pid
        $pid = false;
        exec("ps -ef | awk '\$8==\"Xvfb\" && \$9==\":$displayNum\" {print $2}'",$pid);
        if(!$pid)
        {
            throw new \Exception('Can not start renderer!');
        }
        $pid = $pid[0];
        
        $procRecId = $this->addProcessingRequest($request['projectId'],$request['id'],$displayNum,$pid);
        
        $this->setRequestInProcess($request['id']);
        if(!$this->writeRendererLock($displayNum,$pid,$request,$procRecId))
        {
            $this->error = 'Can not save renderer lock file!';
            return false;
        }
        
        
        return true;        
    }
  
    
    /**
     * Convert video and merge it with audio file if it exist
     * @param array $file array with file specified info
     * @param array $processingRequest array with processing request info
     */
    public function doConvertation($file,$processingRequest)
    {
        
        //First we need to move captured file from download folder to rendered
        $renderedFile = RENDER_RENDERED_DIR.$file['filename'];
        rename($file['pathname'],$renderedFile);
        
        $convertedFile = RENDER_CONVERTED_DIR.$file['basename'].'.mp4';
        
        //Check if exist audio file
        $audio = RENDER_AUDIO_DIR.$file['basename'].'/background.mp3';
        
        $itemsAudio = self::getAudioItemsList(RENDER_AUDIO_DIR.$file['basename']);
        
        if(file_exists($audio))
        {
            //find duration of video
            $duration = self::getVideoDuration($renderedFile);
            $end = $duration>1 ? $duration - 1 : 0; 
            //Then we need to start convertation and merge
            if(!empty($itemsAudio))
            {
                $filter = $this->getFilterCommand($itemsAudio,1);
                $command = 'ffmpeg -i "'.$renderedFile.'" -i "'.$audio.'"  '.$filter['input'].'  -filter_complex "[1]afade=t=in:ss=0:d=1,afade=t=out:st='.$end.':d=1,volume=0.4[a1];'.$filter['filter'].'" -shortest -strict -2 -vcodec libx264 "'.$convertedFile.'" > /dev/null 2>/dev/null &';
                //var_dump($command);
                exec($command);                            
            }else{
                exec('ffmpeg -i "'.$renderedFile.'" -i "'.$audio.'" -af \'afade=t=in:ss=0:d=1,afade=t=out:st='.$end.':d=1,volume=0.4\' -shortest -strict -2 -vcodec libx264 "'.$convertedFile.'" > /dev/null 2>/dev/null &');            
                //exec('ffmpeg -i "'.$renderedFile.'" -i "'.$audio.'" -filter_complex "aevalsrc=0:d=1 [a_silence]; [0:a:0] [a_silence] acrossfade=d=1" -shortest -strict -2 -vcodec libx264 "'.$convertedFile.'" > /dev/null 2>/dev/null &');            
            }
        }elseif(!empty($itemsAudio))
        {
            $filter = $this->getFilterCommand($itemsAudio,0);
            $command = 'ffmpeg -i "'.$renderedFile.'" '.$filter['input'].'  -filter_complex "'.$filter['filter'].'" -shortest -strict -2 -vcodec libx264 "'.$convertedFile.'" > /dev/null 2>/dev/null &';
            //var_dump($command);
            exec ($command);
        }else{
            exec('ffmpeg -i "'.$renderedFile.'" -vcodec libx264 "'.$convertedFile.'" > /dev/null 2>/dev/null &');
        }
      
        $this->updateProcessingStatus($processingRequest['id'],'converting');
        
        return true;
    }  
    
    
    /**
     * Finish process - move to user folder, clear locks, remove records from db
     * @param array $file array with file specified info
     * @param array $processingRequest array with processing request info
     * @param array $project
     */
    public function doFinalize($file,$processingRequest,$project)
    {
        $user = new \Sart\User;
        $user->id = $project['owner'];
        $videoDir = $user->getUserVideoDir();
        
        $filename = sanitize_file_name($project['name']).'.mp4';
        
        rename($file['pathname'],$videoDir.DIRECTORY_SEPARATOR.$filename);
        
        //Remove lock, kill renderers
        $this->removeLock($processingRequest['display'],$processingRequest['pid']);
        
        //Remove from db queues
        $this->updateVideo($processingRequest['requestId'], $filename,$project);

        //$this->removeRequest($processingRequest['requestId']);
        $this->removeProcessingRequest($processingRequest['id']);
        
        //clean audio directory
        
        
        return true;
        
    }
  
    /**
     * Return captured videos
     * @author N.Z. <n.z@software-art.com>
     */
    public function getCaptured()
    {
        $result = [];
        if(!is_dir(RENDER_DOWNLOAD_DIR))
        {
            return [];
        }
        $dir = new \DirectoryIterator(RENDER_DOWNLOAD_DIR );
        foreach ($dir as $node )
        {

            if (!preg_match('#^\.#', $node->getFilename()))
            {

                if ( !$node->isDir()  )
                {
                    list($fname,$suffix) = explode('.',$node->getFilename());
                    list($projectId,$requestId) = explode('_',$fname);
                    $result[] = [
                        'projectId'=>$projectId,
                        'requestId'=>$requestId,
                        'filename'=>$node->getFilename(),
                        'pathname'=>$node->getPathname(),
                        'basename'=>$fname,
                    ];
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Return converted videos
     * @author N.Z. <n.z@software-art.com>
     */
    public function getConverted()
    {
        $result = [];
        $dir = new \DirectoryIterator(RENDER_CONVERTED_DIR);
        foreach ( $dir as $node )
        {
            if (!preg_match('#^\.#', $node->getFilename()))
            {
                if ( !$node->isDir()  )
                {
                    list($projectId,$requestId) = explode('_',$node->getBasename('.mp4'));
                    $result[] = [
                        'projectId'=>$projectId,
                        'requestId'=>$requestId,
                        'filename'=>$node->getFilename(),                        
                        'pathname'=>$node->getPathname(),
                        'basename'=>$node->getBasename('.mp4'),
                    ];
                }
            }
        }
        return $result;
    }    

    /**
     * Add lock file for renderer display
     * @return bool 
     */
    public function writeRendererLock($displayNum,$pid,$request,$procRecId)
    {
        $lockFile = $displayNum.'_'.RENDER_LOCKS_FILENAME;
        $fullPath = RENDER_LOCKS_DIRECTORY.$lockFile;
        if(!file_exists( $fullPath ))
        {
            $data = [
                'time'=>time(),
                'display'=>$displayNum,
                'pid'=>$pid,
                'project'=>$request['projectId'],
                'processingRequestId'=>$procRecId,
                'requestId'=>$request['id']
            ];
            if ( file_put_contents($fullPath, json_encode($data) )) {
                return true;
            }            
        }
        return false;
    }
  
  
    /**
     * Clear Xvfb and remove lock
     * @param int $displayNum render display number
     * @param int $pid  pid of Xvfb process
     */
    public function removeLock($displayNum,$pid) {
        //Kill process
        passthru("kill $pid");
        
        $lockFile = $displayNum.'_'.RENDER_LOCKS_FILENAME;
        $fullPath = RENDER_LOCKS_DIRECTORY.$lockFile;        
        
        //remove lock
        unlink($fullPath);
      return true;
    }


    /**
     *
     */
    public function removeRequest($id) {
        $stmt = $this->db->prepare("DELETE FROM `requests` WHERE `id` = :id");
        return $stmt->execute([
          'id' => $id
        ]);
    }

    /**
     *
     */
    public function removeProcessingRequest($id) {
        $stmt = $this->db->prepare("DELETE FROM `processingRequests` WHERE `id` = :id");
      
        return $stmt->execute([
          'id' => $id
        ]);
    }
  
    /**
     * Update video status and add notification
     */
    public function updateVideo($requestId,$filename,$project)
    {
        $query = $this->db->prepare("SELECT * FROM `videos` WHERE id = (SELECT videoId FROM requests r WHERE r.id = :requestId)");
        
        $result = $query->execute([
          'requestId' => $requestId
        ]);
        
        if(!$result)
        {
            return false;
        }
        $video = $query->fetch();
        
        
        $query = $this->db->prepare("UPDATE videos v, requests r SET v.status = :status, v.fileName = :filename, v.updated = NOW(),r.progress = 100 WHERE v.id = :videoId AND r.id = :requestId ");
        
        $result = $query->execute([
          'videoId' => $video['id'],
          'requestId' => $requestId,
          'filename'=>$filename,
          'status'  => \Sart\Video::STATUS_RENDERED,
        ]);
        
        if($result)
        {
            \Sart\SysNotification::addNotify(\Sart\SysNotification::TYPE_RENDER_DONE,$video['owner'],$video,$project['name']);
            
            if($user = \Sart\User::model()->findByPk($video['owner']))
            {
                $mailer = new \Sart\Mailer;
                $mailer->emailRenderFinished($user,$project['name'],$video);
            }
        }
        
        return $result;
    }
  
  
    /**
     * Check if we have free renderer
     */
    public function getFreeRenderer()
    {
        $xvfbCount = self::getXvfbCount();
        if($xvfbCount < MAX_RENDER_PROCESS )
        {
            return MAX_RENDER_PROCESS - $xvfbCount;
            
        }
        return false;
    }
    
    protected function getFilterCommand($items,$start = 1)
    {
        $input = '';
        $filter = '';
        $n = $start;
        $names = '';
        foreach($items as $item)
        {
            if($item['time'] == 0)
            {
                $item['time'] = 1;
            }
            $n++;
            $input .= ' -i "'.$item['file'].'"';
            $filter .='['.$n.']adelay='.$item['time'].'|'.$item['time'].'[a'.$n.'];';
            $names .= '[a'.$n.']';

        }
        
        $filter .= ($start ? '[a'.$start.']' : '' ).$names.'amix='.$n;
        
        return ['input'=>$input,'filter'=>$filter];
        
    }
  
  
  
    /**
     * Return count of runned Xvfb
     * @author N.Z. <n.z@software-art.com>
     */
    static public function getXvfbCount()
    {
        $output = [];
        exec('pgrep -c Xvfb', $output);
        return (int) $output[0];
    }
    
    /**
     * Return free display num, that can be used to start new renderer
     */
    static public function getFreeDisplayNum()
    {
        for($i=1; $i<= MAX_RENDER_PROCESS; $i++)
        {
            $lockFile = $i.'_'.RENDER_LOCKS_FILENAME;
            if(!file_exists(RENDER_LOCKS_DIRECTORY.$lockFile))
            {
                return $i;
            }
        }
        return false;
    }
    
    /**
     * Return count of runned Xvfb
     * @author N.Z. <n.z@software-art.com>
     */
    static public function getVideoDuration($file)
    {
        $output = [];
        exec('ffprobe -i "' . $file . '" -show_format 2>&1 | sed -n \'s/duration=//p\'', $output);
        return (float) $output[0];
    }    
    
    static public function getAudioItemsList($dir)
    {
        $result = [];
        $files = scandir($dir);
        foreach($files as $f)
        {
            if($f == 'background.mp3' || $f == '.' || $f == '..')
            {
                continue;
            }
            
            list($time,$file) = explode('_',$f,2);
            $result[] = ['file'=>$dir.'/'.$f,'time'=>$time];
            
        }
        
        return $result;
    }
    
}