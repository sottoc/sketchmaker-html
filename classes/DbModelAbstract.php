<?php
namespace Sart;

use spir1donov\Database;

abstract class DbModelAbstract extends ModelAbstract
{

    protected $db;
        
    abstract public function getTableName();
    
    abstract protected function getPk();
    
    public function __construct(\PDO $db = null)
    {
        if($db)
        {
            $this->db = $db;
        }else{
            $this->db = Database::getDb();
        }
        
    }
    
    public function findByPk($value)
    {
        $attr = $this->getPk();
        $where[] = " `$attr` = :$attr";
        $values[$attr] = $value;
        
        $query = $this->db->prepare("SELECT *  FROM `{$this->getTableName()}` WHERE ". implode(' AND ', $where)." LIMIT 1");

        
        $query->execute($values);
        
        $this->_attributes = $query->fetch();
        
        return !empty($this->_attributes) ? $this : null;        
    }
    
    public function findByAttributes($attr,$value = null)
    {
        $where = [];
        $values = [];
        if(is_array($attr))
        {
            foreach($attr as $key => $val)
            {
                $where[] = " `$key` = :$key ";     
                $values[$key] = $val;
            }
        }else{
            $where[] = " `$attr` = :$attr";
            $values[$attr] = $value;
        }
        
        $query = $this->db->prepare("SELECT *  FROM `{$this->getTableName()}` WHERE ". implode(' AND ', $where)." LIMIT 1");

        
        $query->execute($values);
        
        $this->_attributes = $query->fetch();
        
        return !empty($this->_attributes) ? $this : null;
    }
    
    public function findAllByAttributes($attr,$value,$order = false)
    {
        $query = $this->db->prepare("SELECT *  FROM `{$this->getTableName()}` WHERE `'.$attr.'` = :attr".($order ? ' ORDER BY '.$order : ''));
        $query->execute([
            'attr'=> $value
        ]);
        $rows = $query->fetchAll();
        $models = [];
        
        foreach($rows as $row)
        {
            $model = new  get_class($this);  
            $model->setAttributes = $row;
            
            $models[] = $model;
        }
        $this->_attributes = $query->fetch();
        
        return $models;
    }
    
    public function updateAttribute($attr,$val)
    {
        $pkName = $this->getPk();
        $query = $this->db->prepare("UPDATE `".$this->getTableName()."` SET `".$attr."` = :val WHERE `{$pkName}` = :id");
        
        $query->execute([
            $pkName => $this->$pkName,
            'val' => $val
        ]);
    }
    
    
    public function save()
    {
        $pkName = $this->getPk();
        $attrs = $this->_attributes;
        unset($attrs[$pkName]);
        $fields = [];
        $vals = [];
        if(isset($this->{$pkName}) && $this->{$pkName})
        {
            foreach($attrs as $name=>$val)
            {
              $fields[] = "`$name` = :$name";
              $vals[$name] = $val;
            }
            $query = $this->db->prepare("UPDATE `".$this->getTableName()."` SET ".implode(',',$fields)." WHERE `$pkName`=:pk" );
            $result = $query->execute(
                array_merge(
                [
                    ':pk' => $this->$pkName,
                ],
                $vals
                )
            );            
        }else{
            $fields['name'] = [];
            $fields['val'] = [];
            foreach($attrs as $name=>$val)
            {
              $fields['name'][] = "`$name`";
              $fields['val'][] = ":$name";
              $vals[$name] = $val;
            }
            $query = $this->db->prepare("INSERT INTO `".$this->getTableName()."` (".implode(',',$fields['name']).") VALUES(".implode(',',$fields['val']).") " );
            $result = $query->execute($vals);
            if($result)
            {
                $this->_attributes[$pkName] = $this->db->lastInsertId();
            }
        }
        
        if(!$result)
        {
            throw new \Exception($this->db->errorInfo[2]);
        }
        return true;
    }
    
    
    public function delete()
    {
        $pkName = $this->getPk();
        $query = $this->db->prepare("DELETE FROM  `".$this->getTableName()."` WHERE `$pkName`=:pk" );
        $result = $query->execute(
            [
                ':pk' => $this->$pkName,
            ]
        );
        if($result===false)
        {
            throw new \Exception($this->db->errorInfo[2]);
        }
        
        return $result;
    }
    
    
    /**
     * Check if record is new
     * @return bool
     */
    public function getIsNewRecord()
    {
        return !isset($this->{$this->getPk()});
    }
}