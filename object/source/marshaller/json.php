<?php


namespace Components;


  /**
   * Marshaller_Json
   *
   * @package net.evalcode.components
   * @subpackage object.marshaller
   *
   * @author evalcode.net
   */
  class Marshaller_Json extends Marshaller
  {
    /**
     * (non-PHPdoc)
     * @see Components.Marshaller::marshal()
     */
    public function marshal(Serializable $object_)
    {
      if($object_ instanceof Primitive)
        return json_encode($object_->value());

      if($object_ instanceof Serializable_Json)
        return $object_->serializeJson();

      $type=get_class($object_);
      $version=$object_->serialVersionUid();

      $map=Cache::get('components/marshaller/json/'.md5($type)."/$version");

      if(false===$map)
        $map=$this->mapTypes($object_);

      return json_encode($object_);
    }

    /**
     * (non-PHPdoc)
     * @see Components.Marshaller::unmarshal()
     */
    public function unmarshal($data_, $type_)
    {
      $type=new \ReflectionClass($type_);
      if($type->isSubclassOf('Components\\Primitive'))
        return $type_::valueOf(json_decode($data_));

      if($type->isSubclassOf('Components\\Serializable_Json'))
      {
        $instance=new $type_();

        return $instance->unserializeJson($data_);
      }

      return json_decode($data_);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected function mapTypes(Serializable $object_)
    {
      $annotations=Annotations::get(get_class($object_));

      foreach($annotations->getPropertyAnnotations() as $propertyName=>$propertyAnnotations)
      {
        $property=array();
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
                $property['inner']=ltrim(end($chunks), '\\');
                if(Primitive::isNative($property['inner']))
                  $property['inner']=Primitive::asBoxed($property['inner']);
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

        if($serializer=static::getSerializerImplForType($property['type']))
          $property['serializer']=$serializer;

        $properties[$propertyName]=$property;
      }

      var_dump($properties);
      return $properties;
    }
    //--------------------------------------------------------------------------
  }
?>
