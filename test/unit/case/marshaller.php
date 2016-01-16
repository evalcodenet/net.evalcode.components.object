<?php


namespace Components;


  /**
   * Object_Test_Unit_Case_Marshaller
   *
   * @package net.evalcode.components.object
   * @subpackage test.unit.case
   *
   * @author evalcode.net
   */
  class Object_Test_Unit_Case_Marshaller implements Test_Unit_Case
  {
    // TESTS
    /**
     * @test
     * @profile fork
     */
    public function testMarshal()
    {
      $administrators=new Object_Test_Unit_Case_Marshaller_Entity_Role();
      $administrators->id=1;
      $administrators->name='Administrators';
      $administrators->createdAt=Date::forUnixTimestamp(time(), Timezone::forName('Asia/Shanghai'));

      $users=new Object_Test_Unit_Case_Marshaller_Entity_Role();
      $users->id=Integer::valueOf(2);
      $users->name=new String('Users');
      $users->createdAt=Date::now();

      $group=new Object_Test_Unit_Case_Marshaller_Entity_Group();
      $group->id=Integer::valueOf(1);
      $group->name=String::valueOf('Internal');
      $group->createdAt=Date::now()->before(Time::forDays(7));

      $user=new Object_Test_Unit_Case_Marshaller_Entity_User();
      $user->createdAt=Date::now();
      $user->enabled=Boolean::valueOf(true);
      $user->locale=I18n_Locale::en_US();
      $user->name=String::valueOf('woo.hoo');
      $user->roles=HashMap::valueOf(array($administrators, $users));
      $user->groups=HashMap::createEmpty()->put(0, $group);

      print_r($marshaller=Objects::toJson($user));

      return;

      split_time('reset');
      $json=$marshaller->marshal($user);
      split_time('marshal');
      $nUser=$marshaller->unmarshal($json, 'Components\\Object_Test_Unit_Case_Marshaller_Entity_User');
      split_time('unmarshal');
      $json=$marshaller->marshal($user);
      split_time('marshal');
      $nUser=$marshaller->unmarshal($json, 'Components\\Object_Test_Unit_Case_Marshaller_Entity_User');
      split_time('unmarshal');
      $json=$marshaller->marshal($user);
      split_time('marshal');
      $nUser=$marshaller->unmarshal($json, 'Components\\Object_Test_Unit_Case_Marshaller_Entity_User');
      split_time('unmarshal');
      $json=$marshaller->marshal($user);
      split_time('marshal');
      $nUser=$marshaller->unmarshal($json, 'Components\\Object_Test_Unit_Case_Marshaller_Entity_User');
      split_time('unmarshal');

      assertTrue($user->name->equals($nUser->name));
      assertTrue($user->locale->equals($nUser->locale));
      assertTrue($user->createdAt->equals($nUser->createdAt));
      assertTrue($user->enabled->equals($nUser->enabled));
    }
    //--------------------------------------------------------------------------
  }
?>
