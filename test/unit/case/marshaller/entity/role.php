<?php


namespace Components;


  /**
   * Object_Test_Unit_Case_Marshaller_Entity_Role
   *
   * @package net.evalcode.components.object
   * @subpackage test.unit.case.marshaller.entity
   *
   * @author evalcode.net
   */
  class Object_Test_Unit_Case_Marshaller_Entity_Role implements Serializable
  {
    // PROPERTIES
    /**
     * @var integer
     */
    public $id;
    /**
     * @var \Components\String
     */
    public $name;
    /**
     * @var \Components\Date
     */
    public $createdAt;
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Serializable::serialVersionUid() \Components\Serializable::serialVersionUid()
     */
    public function serialVersionUid()
    {
      return 1;
    }
    //--------------------------------------------------------------------------
  }
?>
