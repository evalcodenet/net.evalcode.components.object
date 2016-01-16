<?php


namespace Components;


  /**
   * Object_Test_Unit_Case_Marshaller_Entity_User
   *
   * @package net.evalcode.components.object
   * @subpackage test.unit.case.marshaller.entity
   *
   * @author evalcode.net
   */
  class Object_Test_Unit_Case_Marshaller_Entity_User implements Serializable
  {
    // PROPERTIES
    /**
     * @name created_at
     * @var \Components\Date
     */
    public $createdAt;
    /**
     * @var \Components\I18n_Locale
     */
    public $locale;
    /**
     * @var \Components\String
     */
    public $name;
    /**
     * @var \Components\Boolean
     */
    public $enabled;
    /**
     * @var \Components\Object_Test_Unit_Case_Marshaller_Entity_Role[]
     */
    public $roles;
    /**
     * @var \Components\HashMap[\Components\Object_Test_Unit_Case_Marshaller_Entity_Group]
     */
    public $groups;
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
