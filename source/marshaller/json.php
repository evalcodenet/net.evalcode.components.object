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
  // TODO Optimize & complete ...
  class Object_Marshaller_Json extends Object_Marshaller
  {
    // CONSTRUCTION
    public function __construct()
    {
      $this->m_mapper=new Object_Mapper();
    }
    //--------------------------------------------------------------------------


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
        return json_encode($this->m_mapper->dehydrateObjectOfType($object_, get_class($object_)));

      return json_encode($this->m_mapper->dehydrateObjectArray($object_));
    }

    /**
     * (non-PHPdoc)
     * @see Components\Marshaller::unmarshal()
     */
    public function unmarshal($data_, $type_)
    {
      if(Primitive::isNative($type_))
      {
        $type=Primitive::asBoxed($type_);

        return $type::cast($data_);
      }

      $type=new \ReflectionClass($type_);
      if($type->isSubclassOf('Components\\Value'))
        return $type_::valueOf($data_);

      if($type->isSubclassOf('Components\\Serializable_Json'))
      {
        $instance=new $type_();

        return $instance->unserializeJson($data_);
      }

      return $this->m_mapper->hydrateForType($type_, json_decode($data_, true));
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Object_Mapper
     */
    private $m_mapper;
    //--------------------------------------------------------------------------
  }
?>
