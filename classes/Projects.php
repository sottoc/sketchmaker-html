<?php
namespace spir1donov;

class Projects {
  /** @var \PDO $db */
  private $db;

  private $user;

  function __construct(\PDO $db) {
    $this->db = $db;
  }

  function getProjectsList($ownerId) {
    $listStmt = $this->db->prepare("SELECT `id`, `name`, `data` FROM `projects` WHERE `owner` = ?");
    $listStmt->execute([
      $ownerId
    ]);
    return $listStmt->fetchAll();
  }

  function getProject($projectId) {
    $listStmt = $this->db->prepare("SELECT `id`, `owner`, `name`, `data`, `duration` FROM `projects` WHERE `id` = ?");
    $listStmt->execute([
      $projectId
    ]);
    return $listStmt->fetch();
  }

  function saveProject($projectData) {
    $select = $this->db->prepare("SELECT * FROM `projects` WHERE `name` = :name");
    $insert = $this->db->prepare("INSERT INTO `projects` (`name`, `data`, `owner`, `savedOn`, `duration`) VALUES (:name, :data, :owner, NOW(), :duration)");
    $update = $this->db->prepare("UPDATE `projects` SET `data` = :data, `savedOn` = NOW(), `duration` = :duration WHERE `id` = :id");

    $select->execute(['name' => $projectData['name']]);
    $existing = $select->fetch();

    if ( !is_array( $existing ) ) {
      $insert->execute([
        'name' => $projectData['name'],
        'data' => $projectData['data'],
        'owner' => $projectData['owner'],
        'duration'=>(float)str_replace(['.',','],'.',$projectData['duration'])
      ]);

      return $this->db->lastInsertId();
    } elseif ($existing['owner'] === $projectData['owner']) {
      $update->execute([
        'data' => $projectData['data'],
        'id' => $existing['id'],
        'duration'=>(float)str_replace(['.',','],'.',$projectData['duration'])
      ]);

      return $existing['id'];
    } else {
      return false;
    }
  }
  
    /**
     * Get time->audio map from stage json object
     */
    function getAudioMap($stage){
        $result = [];
        $currentTime = 0;
        $slideTransitionTime = !empty($stage->attrs) && (!empty($stage->attrs->slideTransitionIn) || !empty($stage->attrs->slideTransitionOut) ) ? 0.5 : 0;   
        $delayBetweenElements = 0.5;
        foreach($stage->children as $layer){
            if($layer->className == 'Layer' && (!empty($layer->attrs) && $layer->attrs->name =='slide'))
            {
                //var_dump($layer);
                foreach($layer->children as $child)
                {
                    if(!empty($child->attrs) &&  !empty($child->attrs->beforeNext))
                    {
                        if(!empty($child->attrs->attached_mp3))
                        {
                            $result[] = ['file'=>$child->attrs->attached_mp3,'time'=>$currentTime];
                        }
                        $currentTime += $child->attrs->beforeNext + $delayBetweenElements;
                    }
                }
                $currentTime += $slideTransitionTime;
            }
        }
        //Remove 0.5 for last slide
        $currentTime -= $slideTransitionTime;
        
        return $result;
        
    }
  
}