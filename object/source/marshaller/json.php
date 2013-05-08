<?php


namespace Components;


  /**
   * Object_Marshaller_Json
   *
   * @package net.evalcode.components
   * @subpackage object.marshaller
   *
   * @author evalcode.net
   */
  class Object_Marshaller_Json extends Object_Marshaller
  {
    /**
     * (non-PHPdoc)
     * @see Components.Object_Marshaller::marshal()
     */
    public function marshal($object_)
    {
      if($object_ instanceof Serializable_Json)
        return $object_->serializeJson();

      return json_encode($object_);
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object_Marshaller::unmarshal()
     */
    public function unmarshal($data_, $type_)
    {
      $type=new \ReflectionClass($type_);
      if($type->isSubclassOf('\\Components\\Primitive'))
        return $type_::valueOf(json_decode($data_));

      if($type->isSubclassOf('\\Components\\Serializable_Json'))
      {
        $instance=new $type_();

        return $instance->unserializeJson($data_);
      }

      return json_decode($data_);
    }
    //--------------------------------------------------------------------------
  }
?>
