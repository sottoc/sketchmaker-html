<?php
/**
 * Model to handle users
 * @author N. Z. <n.z@software-art.com>
 * @package CanvasSketchMaker
 */
namespace Sart;

class User extends DbModelAbstract{
    
    /**
     * Return table name
     */
    public function getTableName()
    {
        return 'users';
    }
    
    /**
     * Return table primary key
     */
    public function getPk()
    {
        return 'id';
    }
    
    
    public function getFullName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
    
    
    /**
     * Get user dir path
     */
    public function getUserDir()
    {
        $dir = APP_PATH.DIRECTORY_SEPARATOR.'media_bin'.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, str_split(str_pad($this->id, 12, '0', STR_PAD_LEFT), 3));
        \Sart\FileHelper::createDirIfNotExist($dir);
        return $dir;
    }
    
    /**
     * Get user url
     */
    public function getUserUrl()
    {
        return str_replace([APP_PATH,'media_bin'],['','storage'],$this->getUserDir());    
    }
    
    /**
     * Return path to user video directory
     */
    public function getUserVideoDir()
    {
        $dir = $this->getUserDir().DIRECTORY_SEPARATOR.'video';
        \Sart\FileHelper::createDirIfNotExist($dir);
        return $dir;
    }
    
    public function getUserVideoUrl()
    {
        return str_replace([APP_PATH,'media_bin'],['','storage'],$this->getUserVideoDir());    
    }
    
	public function setId($id)
    {
        $this->_attributes['id'] = $id;
    }
    
    
    public function toggleAdmin()
    {
        $query = $this->db->prepare("UPDATE `".$this->getTableName()."` SET isAdmin = IF(isAdmin=1, 0, 1) WHERE `id`=:pk" );
        $result = $query->execute(
            [
                ':pk' => $this->id,
            ]
        );                    
    }
    
    public function getTotalCount()
    {
        $query =  $this->db->prepare("SELECT count(*) as total FROM ".$this->getTableName() );
        $query->execute();
        
        $result = $query->fetch();
        return $result['total'];
        
    }
    
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}    
}