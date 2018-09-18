<?php
/**
 * Model to handle different system events and notifactions ( render status, other messages)
 * @author N. Z. <n.z@software-art.com>
 * @package CanvasSketchMaker
 */
namespace Sart;

class SysNotification extends DbModelAbstract{
    
    const TYPE_UPDATE_PROGRESS = 'update_progress';
    
    const TYPE_RENDER_DONE = 'render_done';
    
    /**
     * Return table name
     */
    public function getTableName()
    {
        return 'sysNotifications';
    }
    
    /**
     * Return table primary key
     */
    public function getPk()
    {
        return 'id';
    }
    
    public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    /**
     * @param add_info 
     */
    public static function addNotify($type,$ownerId, $meta,$add_info = null)
    {
        $notify = SysNotification::model();
        $notify->created = gmdate('U');
        $notify->owner = $ownerId;
        $notify->type = $type;
        
        switch($notify->type)
        {
            case self::TYPE_RENDER_DONE:
                $video = new \Sart\Video;
                $video->setAttributes($meta);
                $notify->meta  = serialize([
                    'id'=>$video->id,
                    'url'=>$video->getDownloadLink(),
                    'project_name'=>$add_info,
                ]);
                break;
            case self::TYPE_UPDATE_PROGRESS:
                $notify->meta  = serialize($meta);
                break;            
            default:
                break;
        }
        
        if(!$notify->save())
        {
            return false;
        }
        return $notify;
    }

    /**
     * Return all notifications for specified owner and timestamp
     * @param int $ownerId
     * @param int $timestamp unix timestamp
     * @return array
     */ 
    public static function getNotificationsForOwner($ownerId,$timestamp)
    {
        $order  = $order ? $order : 'n.created ASC';
        $notify = self::model();
        $query = $notify->db->prepare(
        "SELECT *  FROM `{$notify->getTableName()}` n
        WHERE n.`owner` = :owner AND n.`created` >:timestamp
        ORDER BY $order
        ");
        $result = $query->execute([
            'owner'=> $ownerId,
            'timestamp' => $timestamp
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