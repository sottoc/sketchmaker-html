<?php
/**
 * Model to handle videos
 * @author N. Z. <n.z@software-art.com>
 * @package CanvasSketchMaker
 */
namespace Sart;

class Video extends DbModelAbstract{
    
    const STATUS_IN_QUEUE = 'in queue';
    const STATUS_IN_PROCESS = 'in process';
    const STATUS_RENDERED = 'rendered';
    
    /**
     * Return table name
     */
    public function getTableName()
    {
        return 'videos';
    }
    
    /**
     * Return table primary key
     */
    public function getPk()
    {
        return 'id';
    }
    
   
	public function setId($id)
    {
        $this->_attributes['id'] = $id;
    }
    
    /**
     * Return status of video
     */
    public function getStatusString()
    {
        return $this->status == self::STATUS_IN_PROCESS ? ( isset($this->processing_status) ? $this->processing_status : $this->status ) : $this->status;
    }
    
    
    public function getDownloadLink()
    {
        if(isset($this->owner))
        {
            $user = new \Sart\User();
            $user->id = $this->owner;
            
            return $user->getUserVideoUrl().'/'.$this->fileName;//.$this->fileFormat;
        }
        return false;
    }
    
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    /**
     * Return set of videos to populate grid
     * @param int $ownerId
     * @return array
     */ 
    public static function getVideosForGrid($ownerId,$order = 'v.updated DESC')
    {
        $order  = $order ? $order : 'v.created DESC';
        $video = self::model();
        $query = $video->db->prepare(
        "SELECT DISTINCT v.id as id, v.owner, v.fileName, v.status, v.created, v.updated, p.name,v.videoResolution,v.fileFormat, r.progress  FROM `{$video->getTableName()}` v
        LEFT JOIN `requests`  r ON r.id =(SELECT sr.id FROM `requests` sr WHERE sr.videoId = v.id ORDER BY id DESC LIMIT 1)
        LEFT JOIN `projects` p ON v.project = p.id
        WHERE v.`owner` = :owner
        ORDER BY $order
        ");
        $result = $query->execute([
            'owner'=> $ownerId
        ]);
        if(!$result)
        {
            throw new \Exception($this->db->errorInfo[2]);
        }
        $rows = $query->fetchAll();
        $models = [];
        
        foreach($rows as $row)
        {
            $model = Video::model();
            
            $model->setAttributes($row);
            
            $models[] = $model;
        }
        return $models;        
    }
    
    
    
}