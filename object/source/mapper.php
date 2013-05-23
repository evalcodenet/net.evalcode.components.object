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
     *
     * @return array|scalar
     *
     * @throws Exception_IllegalArgument
     */
    public function map($object_)
    {
      if(is_object($object_))
        return $this->mapObjectOfType($object_, get_class($object_));

      if(is_array($object_))
        return $this->mapObjectArray($object_);

      throw new Exception_IllegalArgument('object/mapper', 'Can not map given argument to array.');
    }

    /**
     * @param array|scalar $data_
     * @param string $type_
     *
     * @return mixed
     */
    public function unmap(array $data_, $type_)
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

    /**
     * @param array|mixed $array_
     *
     * @return array|scalar
     */
    public function mapObjectArray(array $array_)
    {
      $data=array();
      foreach($array_ as $key=>$value)
      {
        if(is_scalar($value) || is_null($value))
          $data[$key]=$value;
        else if(is_array($value))
          $data[$key]=$this->mapObjectArray($value);
        else
          $data[$key]=$this->mapObjectOfType($value, get_class($value));
      }

      return $data;
    }

    /**
     * @param mixed $object_
     * @param string $type_
     *
     * @return array|scalar
     */
    public function mapObjectOfType($object_, $type_)
    {
      if($object_ instanceof Collection)
        return $this->mapObjectArray($object_->arrayValue());

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
          $data[$info['name']]=$this->mapObjectArray($value);
        else
          $data[$info['name']]=$this->mapObjectOfType($value, $info['type']);
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
    //-----


    protected function propertyMap($type_)
    {
      if($map=Cache::get('components/object/mapper/'.md5($type_)))
        return $map;

      $annotations=Annotations::get($type_);

      $map=array();
      foreach($annotations->getPropertyAnnotations() as $propertyName=>$propertyAnnotations)
      {
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

      return $map;
    }
    //--------------------------------------------------------------------------
  }
?>
