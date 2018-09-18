<?php
/**
 * Abstract class with base model properties and methods
 */
namespace Sart;

abstract class ModelAbstract {

    protected $_notAllowedAttr = [
        '_attributes'
    ];
    
    protected $_attributes = [];
    
    public function __get($name)
    {
        $getter='get'.$name;
		if(method_exists($this,$getter))
        {
			return $this->$getter();
        }
        elseif(array_key_exists($name, $this->_attributes))
        {
            return $this->_attributes[$name];
        }
        throw new \Exception('No attribute '.$name.' in class '.get_class($this) );
    }
    
    
    public function __set($name,$value)
	{
		$setter='set'.$name;
		if(method_exists($this,$setter))
			return $this->$setter($value);
        else{
            if(!in_array($name,$this->_notAllowedAttr))
            {
                $this->_attributes[$name] = $value;
                return $value;
            }
        }
        throw new \Exception('Can not set attribute '.$name.' in class '.get_class($this) );
        
    }
    
    public function __isset($name)
	{
		$getter='get'.$name;
		if(method_exists($this,$getter))
        {
			return $this->$getter()!==null;
        }
        else
        {
            return isset($this->_attributes[$name]);
        }
    }
    
    public function __unset($name)
    {
        $setter='set'.$name;
		if(method_exists($this,$setter))
			$this->$setter(null);
        elseif(isset($this->_attributes[$name]))
        {
            unset($this->_attributes[$name]);
        }
    }

    
    public function getAttributes()
    {
        return $this->_attributes;
    }
    
    public function setAttributes($attrs)
    {
        $this->_attributes = $attrs;
    }
    
    
	public static function model($className=__CLASS__)
	{
		return new $className;
	}
    
}