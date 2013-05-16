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
      if(Primitive::isNative(gettype($object_)))
        return json_encode($object_);

      if($object_ instanceof Value)
        return json_encode($object_->value());

      if($object_ instanceof Serializable_Json)
        return $object_->serializeJson();

      $type=get_class($object_);

      $values=array();
      foreach($this->propertyMap($type) as $property=>$info)
      {
        if(Primitive::isNative($info['type']))
          $values[$info['name']]=$object_->$property;
        else if($object_->$property instanceof Value)
          $values[$info['name']]=$object_->$property->value();

        // TODO Deep mapping / map arrays/objects ...
      }

      return json_encode($values);
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
  }
?>
