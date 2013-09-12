<?php


namespace Components;


  /**
   * Object_Mappable
   *
   * @package net.evalcode.components.object
   * @subpackage object
   *
   * @author evalcode.net
   *
   * @api
   */
  interface Object_Mappable extends Object
  {
    // ACCESSORS
    /**
     * @return \Components\Object_Properties
     */
    function properties();
    //--------------------------------------------------------------------------
  }
?>
