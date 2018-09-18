<?php
/**
 * Model to handle requests
 * @author N. Z. <n.z@software-art.com>
 * @package CanvasSketchMaker
 */
namespace Sart;

class Request extends DbModelAbstract{
    /**
     * Return table name
     */
    public function getTableName()
    {
        return 'requests';
    }
    
    /**
     * Return table primary key
     */
    public function getPk()
    {
        return 'id';
    }
    
    
    public function calculateProgress($padding = 10)
    {
        $this->progress =ceil( ($this->renderedFrames / $this->totalFrames)*100 - $padding );
    }
    
    public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    //public static function updateFrames($requestId,$frames)
    //{
    //    $request = self::model();
    //    
    //    $query = $request->db->prepare("UPDATE {$request->getTableName()}  SET renderedFrames = :frames WHERE id = :requestId ");
    //    
    //    $result = $query->execute([
    //      'requestId' =>(int)$requestId,
    //      'renderedFrames'=>(int)$frames,
    //    ]);
    //   
    //    return $result;
    //
    //}
}