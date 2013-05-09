<?php


namespace Components;


  /**
   * Serializer
   *
   * @package net.evalcode.components
   * @subpackage object
   *
   * @author evalcode.net
   */
  interface Serializer
  {
    // ACCESSORS
    /**
     * @param Components\Serializable $object_
     *
     * @return string
     */
    function serialize(Serializable $object_);

    /**
     * @param string $data_
     * @param string $type_
     *
     * @return Components\Serializable
     */
    function deserialize($data_, $type_);
    //--------------------------------------------------------------------------
  }
?>
