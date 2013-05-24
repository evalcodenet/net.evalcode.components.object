<?php


namespace Components;


  /**
   * Object_Mapper
   *
   * @package net.evalcode.components
   * @subpackage object
   *
   * @author evalcode.net
   */
  class Object_Mapper
  {
    // ACCESSORS/MUTATORS
    /**
     * @param mixed $object_
     * @param array|scalar $data_
     */
    public function hydrate($object_, array $data_)
    {
      $type=get_class($object_);
      foreach($this->propertyMap($type) as $property=>$info)
      {
        if(false===isset($data_[$info['name']]))
          continue;

        $t=$info['type'];

        if(Primitive::isNative($t))
        {
          $object_->$property=$data_[$info['name']];
        }
        else
        {
          $o=new \ReflectionClass($t);
          if($o->isSubclassOf('Components\\Value'))
            $object_->$property=$t::valueOf($data_[$info['name']]);
        }
      }

      // TODO Recursive mapping ...
    }

    /**
     * @param array|scalar $data_
     * @param string $type_
     *
     * @return mixed
     */
    public function hydrateForType($type_, array $data_)
    {
      $object=new $type_();
      foreach($this->propertyMap($type_) as $property=>$info)
      {
        if(false===isset($data_[$info['name']]))
          continue;

        $t=$info['type'];

        if(Primitive::isNative($t))
        {
          $object->$property=$data_[$info['name']];
        }
        else
        {
          $o=new \ReflectionClass($t);
          if($o->isSubclassOf('Components\\Value'))
            $object->$property=$t::valueOf($data_[$info['name']]);
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
     * @param array|mixed $array_
     *
     * @return array|scalar
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
     * @return array|scalar
     */
    public function dehydrateObjectOfType($object_, $type_)
    {
      if($object_ instanceof Collection)
        return $this->dehydrateObjectArray($object_->arrayValue());

      $map=$this->propertyMap($type_);

      $data=array();
      foreach($map as $property=>$info)
      {
        $value=$object_->$property;

        if(is_scalar($value) || is_null($value))
          $data[$info['name']]=$value;
        else if($value instanceof Value)
          $data[$info['name']]=$value->value();
        else if(is_array($value))
          $data[$info['name']]=$this->dehydrateObjectArray($value);
        else
          $data[$info['name']]=$this->dehydrateObjectOfType($value, $info['type']);
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
    private static $m_propertyMap=array();
    //-----


    protected function propertyMap($type_)
    {
      if(isset(self::$m_propertyMap[$type_]))
        return self::$m_propertyMap[$type_];

      if($map=Cache::get('components/object/mapper/'.md5($type_)))
        return self::$m_propertyMap[$type_]=$map;

      $annotations=Annotations::get($type_);

      $map=array();
      foreach($annotations->getPropertyAnnotations() as $propertyName=>$propertyAnnotations)
      {
        if(isset($propertyAnnotations[Annotation_Transient::NAME]))
          continue;

        $property=array(
          'name'=>$propertyName
        );

        foreach($propertyAnnotations as $annotation)
        {
          if($annotation instanceof Annotation_Type)
          {
            if(false===strpos($annotation->value, '|'))
            {
              $property['type']=$annotation->value;
              if(Primitive::isNative($property['type']))
                $property['type']=Primitive::asBoxed($property['type']);
            }
            else
            {
              $chunks=explode('|', $annotation->value);
              if(HashMap::TYPE_NATIVE===Primitive::asNative(ltrim(reset($chunks), '\\')))
              {
                $property['type']=HashMap::TYPE;
                $property['args']=ltrim(end($chunks), '\\');
                if(Primitive::isNative($property['args']))
                  $property['args']=Primitive::asBoxed($property['args']);
              }
              else
              {
                $property['type']=reset($chunks);
                if(Primitive::isNative($property['type']))
                  $property['type']=Primitive::asBoxed($property['type']);
              }
            }
          }

          if($annotation instanceof Annotation_Name)
            $property['name']=$annotation->value;
        }

        $map[$propertyName]=$property;
      }

      Cache::set('components/object/mapper/'.md5($type_), $map);

      return self::$m_propertyMap[$type_]=$map;
    }
    //--------------------------------------------------------------------------
  }
?>
