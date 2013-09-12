<?php


namespace Components;


  /**
   * Object_Properties
   *
   * @package net.evalcode.components.object
   * @subpackage object
   *
   * @author evalcode.net
   */
  class Object_Properties extends Properties
  {
    // PROPERTIES
    /**
     * @var string
     */
    public $type;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($type_)
    {
      $this->type=$type_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACESSORS
    /**
     * @param string $type_
     *
     * @return \Components\Object_Properties
     */
    public static function forType($type_)
    {
      $instance=new static($type_);

      foreach(self::arrayForType($type_) as $property)
        $instance->add($property['name'], $property['type'], $property['nameMapped'], $property['typeMapped']);

      return $instance;
    }

    /**
     * @param string $type_
     *
     * @return \Components\Object_Properties
     */
    public static function emptyForType($type_)
    {
      return new static($type_);
    }

    /**
     * @param string $type_
     *
     * @return array|string
     */
    public static function arrayForType($type_)
    {
      if(isset(self::$m_cache[$type_]))
        return self::$m_cache[$type_];

      if($map=Cache::get('components/object/properties/'.md5($type_)))
        return self::$m_cache[$type_]=$map;

      $annotations=Annotations::get($type_);

      $map=array();
      foreach($annotations->getPropertyAnnotations() as $propertyName=>$propertyAnnotations)
      {
        if(isset($propertyAnnotations[Annotation_Transient::NAME]))
          continue;

        $property=array(
          'name'=>$propertyName,
          'type'=>null,
          'nameMapped'=>$propertyName,
          'typeMapped'=>null
        );

        foreach($propertyAnnotations as $annotation)
        {
          if($annotation instanceof Annotation_Type)
          {
            if(false===strpos($annotation->value, '['))
            {
              $property['type']=$annotation->value;
              if(Primitive::isNative($property['type']))
                $property['type']=Primitive::asBoxed($property['type']);
            }
            else
            {
              if(false!==($pos=strpos($annotation->value, '[]')))
              {
                $property['type']=HashMap::TYPE;
                $property['args']=ltrim(substr($annotation->value, $pos), '\\');
                if(Primitive::isNative($property['args']))
                  $property['args']=Primitive::asBoxed($property['args']);
              }
              else
              {
                $annotationValue=rtrim($annotation->value, ']');
                $property['type']=ltrm(substr($annotationValue, 0, strpos($annotationValue, '[')), '\\');
                $property['args']=ltrim(substr($annotationValue, strpos($annotationValue, '[')+1), '\\');
                if(Primitive::isNative($property['type']))
                  $property['type']=Primitive::asBoxed($property['type']);
                if(Primitive::isNative($property['args']))
                  $property['args']=Primitive::asBoxed($property['args']);
              }
            }
          }

          if($annotation instanceof Annotation_Name)
            $property['nameMapped']=$annotation->value;
        }

        $map[$propertyName]=$property;
      }

      Cache::set('components/object/properties/'.md5($type_), $map);

      return self::$m_cache[$type_]=$map;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return string[]
     */
    public function propertyNames()
    {
      return array_keys($this->m_properties);
    }

    /**
     * @param unknown $name_
     * @param unknown $type_
     * @param string $nameMapped_
     * @param string $typeMapped_
     *
     * @return \Components\Object_Property
     */
    public function add($name_, $type_, $nameMapped_=null, $typeMapped_=null)
    {
      $this->$name_=Object_Property::create($name_, $type_, $nameMapped_, $typeMapped_);

      return $this->$name_;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_cache=array();
    //--------------------------------------------------------------------------
  }
?>
