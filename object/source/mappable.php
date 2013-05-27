<?php


namespace Components;


  /**
   * Object_Mappable
   *
   * @package net.evalcode.components
   * @subpackage object
   *
   * @author evalcode.net
   */
  interface Object_Mappable extends Object
  {
    // ACCESSORS/MUTATORS
    /**
     * @return \Components\Object_Properties
     */
    function properties();
    //--------------------------------------------------------------------------
  }
?>
