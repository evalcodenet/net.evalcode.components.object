<?php


namespace Components;


  /**
   * Object_Test_Unit_Suite
   *
   * @package net.evalcode.components.object
   * @subpackage test.unit
   *
   * @author evalcode.net
   */
  class Object_Test_Unit_Suite implements Test_Unit_Suite
  {
    // OVERRIDES
    public function name()
    {
      return 'object/test/unit/suite';
    }

    public function cases()
    {
      return array(
        'Components\\Object_Test_Unit_Case_Marshaller'
      );
    }
    //--------------------------------------------------------------------------
  }
?>
