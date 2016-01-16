<?php


namespace Components;


  /**
   * Object_Test_Unit_Case_Marshaller_Entity_Group
   *
   * @package net.evalcode.components.object
   * @subpackage test.unit.case.marshaller.entity
   *
   * @author evalcode.net
   *
   * @application test
   */
  class Object_Test_Unit_Case_Marshaller_Entity_Group implements Serializable
  {
    // PROPERTIES
    /**
     * @var \Components\Integer
     */
    public $id;
    /**
     * @Column size=255
     * @var \Components\String
     */
    public $name;
    /**
     * @column created_at
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
