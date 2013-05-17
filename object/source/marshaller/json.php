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
  // TODO Optimize & complete ...
  class Marshaller_Json extends Marshaller
  {
    /**
     * (non-PHPdoc)
     * @see Components\Marshaller::marshal()
     */
    public function marshal($object_)
    {
      if(is_scalar($object_))
        return json_encode($object_);
      if($object_ instanceof Value)
        return json_encode($object_->value());
      if($object_ instanceof Serializable_Json)
        return $object_->serializeJson();

      if(is_object($object_))
        return json_encode($this->mapObject($object_, get_class($object_)));

      return json_encode($mapped=$this->mapArray($object_));
    }

    /**
     * (non-PHPdoc)
     * @see Components\Marshaller::unmarshal()
     */
    public function unmarshal($data_, $type_)
    {
      if(Primitive::isNative($type_))
        return json_decode($data_);

      $type=new \ReflectionClass($type_);
      if($type->isSubclassOf('Components\\Value'))
        return $type_::valueOf(json_decode($data_));

      if($type->isSubclassOf('Components\\Serializable_Json'))
      {
        $instance=new $type_();

        return $instance->unserializeJson($data_);
      }

      $object=new $type_();
      $data=json_decode($data_, true);

      foreach($this->propertyMap($type_) as $property=>$info)
      {
        if(false===isset($data[$info['name']]))
          continue;

        $t=$info['type'];

        if(Primitive::isNative($t))
        {
          $object->$property=$data[$info['name']];
        }
        else
        {
          $o=new \ReflectionClass($t);
          if($o->isSubclassOf('Components\\Value'))
            $object->$property=$t::valueOf($data[$info['name']]);
        }
      }

      // TODO Deep mapping / map arrays/objects ...

      return $object;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_mapper=array();
    //-----


    protected function mapArray(array $array_)
    {
      $data=array();
      foreach($array_ as $key=>$value)
      {
        if(is_scalar($value))
          $data[$key]=$value;
        else if(is_array($value))
          $data[$key]=$this->mapArray($value);
        else
          $data[$key]=$this->mapObject($value, get_class($value));
      }

      return $data;
    }

    protected function mapObject($object_, $type_)
    {
      if($object_ instanceof Collection)
        return $this->mapArray($object_->arrayValue());

      $map=$this->propertyMap($type_);

      $data=array();
      foreach($map as $property=>$info)
      {
        $value=$object_->$property;

        if(is_scalar($value))
          $data[$info['name']]=$value;
        else if($value instanceof Value)
          $data[$info['name']]=$value->value();
        else if(is_array($value))
          $data[$info['name']]=$this->mapArray($value);
        else
          $data[$info['name']]=$this->mapObject($value, $info['type']);
      }

      return $data;
    }
    //--------------------------------------------------------------------------
  }
?>
