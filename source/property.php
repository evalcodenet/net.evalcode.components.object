<?php


namespace Components;


  /**
   * Object_Property
   *
   * @package net.evalcode.components
   * @subpackage object
   *
   * @author evalcode.net
   */
  class Object_Property implements Object, Serializable_Php
  {
    // PROPERTIES
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $nameMapped;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $typeMapped;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $name_
     * @param string $type_
     * @param string $panel_
     *
     * @return \Components\Object_Property
     */
    public static function create($name_, $type_, $nameMapped_=null, $typeMapped_=null)
    {
      $instance=new static();
      $instance->name=$name_;
      $instance->type=$type_;
      $instance->nameMapped=$nameMapped_;
      $instance->typeMapped=$typeMapped_;

      return $instance;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @param string $value_
     *
     * @return \Components\Persistence_Property
     */
    public function name($value_=null)
    {
      if(null===$value_)
        return $this->name;

      $this->name=$value_;

      return $this;
    }

    /**
     * @param string $value_
     *
     * @return \Components\Persistence_Property
     */
    public function type($value_=null)
    {
      if(null===$value_)
        return $this->type;

      $this->type=$value_;

      return $this;
    }

    /**
     * @param string $value_
     *
     * @return \Components\Persistence_Property
     */
    public function nameMapped($value_=null)
    {
      if(null===$value_)
        return $this->nameMapped;

      $this->nameMapped=$value_;

      return $this;
    }

    /**
     * @param string $value_
     *
     * @return \Components\Persistence_Property
     */
    public function typeMapped($value_=null)
    {
      if(null===$value_)
        return $this->typeMapped;

      $this->typeMapped=$value_;

      return $this;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * (non-PHPdoc)
     * @see \Components\Serializable_Php::__sleep()
     */
    public function __sleep()
    {
      return array(
        'name',
        'nameMapped',
        'type',
        'typeMapped',
      );
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Serializable_Php::__wakeup()
     */
    public function __wakeup()
    {

    }

    /**
     * (non-PHPdoc)
     * @see \Components\Serializable::serialVersionUid()
     */
    public function serialVersionUid()
    {
      return 1;
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
      {
        return $this->name===$object_->name
          && $this->type===$object_->type
          && $this->nameMapped===$object_->nameMapped
          && $this->typeMapped===$object_->typeMapped;
      }

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Object::__toString()
     */
    public function __toString()
    {
      return Objects::toString($this);
    }
    //--------------------------------------------------------------------------
  }
?>
