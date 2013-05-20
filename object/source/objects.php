<?php


namespace Components;


  /**
   * Objects
   *
   * @package net.evalcode.components
   * @subpackage object
   *
   * @author evalcode.net
   */
  class Objects
  {
    // STATIC ACCESSORS
    /**
     * @param mixed $object_
     *
     * @return string
     */
    public static function toString($object_)
    {
      return sprintf('%s@%s{%s}',
        get_class($object_),
        object_hash($object_),
        Arrays::toString(get_object_vars($object_))
      );
    }
    //--------------------------------------------------------------------------
  }
?>
