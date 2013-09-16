<?php


namespace Components;


  /**
   * Object_Mapper
   *
   * @package net.evalcode.components.object
   * @subpackage object
   *
   * @author evalcode.net
   *
   * @api
   */
  class Object_Mapper
  {
    // ACCESSORS
    /**
     * @param mixed $object_
     * @param scalar[] $data_
     */
    public function hydrate($object_, array $data_)
    {
      if($object_ instanceof Object_Mappable)
        $properties=$object_->properties();
      else
        $properties=Object_Properties::forType(get_class($object_));

      foreach($properties->propertyNames() as $propertyName)
      {
        /* @var $property \Components\Object_Property */
        $property=$properties->$propertyName;
        if(false===isset($data_[$property->nameMapped]))
          continue;

        $t=$property->type;

        if(Primitive::isNative($t))
        {
          $object_->$property=$data_[$property->nameMapped];
        }
        else
        {
          $o=new \ReflectionClass($t);
          if($o->isSubclassOf('Components\\Value'))
            $object_->$property=$t::valueOf($data_[$property->nameMapped]);
        }
      }

      // TODO Recursive mapping ...
    }

    /**
     * @param scalar[] $data_
     * @param string $type_
     *
     * @return mixed
     */
    public function hydrateForType($type_, array $data_)
    {
      $object=new $type_();

      if($object instanceof Object_Mappable)
        $properties=$object->properties();
      else
        $properties=Object_Properties::forType($type_);

      foreach($properties->propertyNames() as $propertyName)
      {
        /* @var $property \Components\Object_Property */
        $property=$properties->$propertyName;
        if(false===isset($data_[$property->nameMapped]))
          continue;

        $t=$property->type;

        if(Primitive::isNative($t))
        {
          $object->$property=$data_[$property->nameMapped];
        }
        else
        {
          $o=new \ReflectionClass($t);
          if($o->isSubclassOf('Components\\Value'))
            $object->$property=$t::valueOf($data_[$property->nameMapped]);
        }
      }

      // TODO Recursive mapping ...

      return $object;
    }

    public function dehydrate($object_)
    {
      if(is_object($object_))
        return $this->dehydrateObjectOfType($object_, get_class($object_));

      if(is_array($object_))
        return $this->dehydrateObjectArray($object_);

      throw new Exception_IllegalArgument('object/mapper', 'Can not map given argument to array.');
    }

    /**
     * @param array $array_
     *
     * @return scalar[]
     */
    public function dehydrateObjectArray(array $array_)
    {
      $data=array();
      foreach($array_ as $key=>$value)
      {
        if(is_scalar($value) || is_null($value))
          $data[$key]=$value;
        else if(is_array($value))
          $data[$key]=$this->dehydrateObjectArray($value);
        else
          $data[$key]=$this->dehydrateObjectOfType($value, get_class($value));
      }

      return $data;
    }

    /**
     * @param mixed $object_
     * @param string $type_
     *
     * @return scalar[]
     */
    public function dehydrateObjectOfType($object_, $type_)
    {
      if($object_ instanceof Value)
        return $object_->value();
      if($object_ instanceof Collection)
        return $this->dehydrateObjectArray($object_->arrayValue());

      if($object_ instanceof Object_Mappable)
        $properties=$object_->properties();
      else
        $properties=Object_Properties::forType($type_);

      $data=array();
      foreach($properties->propertyNames() as $propertyName)
      {
        /* @var $property \Components\Object_Property */
        $property=$properties->$propertyName;
        $value=$object_->{$property->name};

        if(is_scalar($value) || is_null($value))
          $data[$property->nameMapped]=$value;
        else if($value instanceof Value)
          $data[$property->nameMapped]=$value->value();
        else if(is_array($value))
          $data[$property->nameMapped]=$this->dehydrateObjectArray($value);
        else
          $data[$property->nameMapped]=$this->dehydrateObjectOfType($value, $property->type);
      }

      return $data;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_map=array(
      HashMap::TYPE=>'Components\\Object_Mapper::mapHashmap',
      HashMap::TYPE_NATIVE=>'Components\\Object_Mapper::mapArray'
    );
    private static $m_unmap=array(
      HashMap::TYPE=>'Components\\Object_Mapper::unmapHashmap',
      HashMap::TYPE_NATIVE=>'Components\\Object_Mapper::unmapArray'
    );
    //--------------------------------------------------------------------------
  }
?>
