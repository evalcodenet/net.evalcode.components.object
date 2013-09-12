<?php


namespace Components;


  /**
   * Objects
   *
   * @package net.evalcode.components.object
   *
   * @author evalcode.net
   *
   * @api
   */
  class Objects
  {
    // STATIC ACCESSORS
    /**
    * @param mixed $object_
    * @param scalar[] $data_
    */
    public static function hydrate($object_, array $data_)
    {
      if(null===self::$m_mapper)
        self::$m_mapper=new Object_Mapper();

      return self::$m_mapper->hydrate($object_, $data_);
    }

    /**
     * @param scalar[] $data_
     * @param string $type_
     *
     * @return mixed
     */
    public static function hydrateForType($type_, array $data_)
    {
      if(null===self::$m_mapper)
        self::$m_mapper=new Object_Mapper();

      return self::$m_mapper->hydrateForType($type_, $data_);
    }

    /**
     * @param mixed $object_
     *
     * @return scalar[]
     */
    public static function dehydrate($object_)
    {
      if(null===self::$m_mapper)
        self::$m_mapper=new Object_Mapper();

      return self::$m_mapper->dehydrate($object_);
    }

    /**
     * @param mixed $object_
     *
     * @return string
     */
    public static function toJson($object_)
    {
      return Object_Marshaller::forMimetype(Io_Mimetype::APPLICATION_JSON())->marshal($object_);
    }

    /**
     * @param string $json_
     * @param string $type_
     *
     * @return mixed
     */
    public static function forJson($json_, $type_)
    {
      return Object_Marshaller::forMimetype(Io_Mimetype::APPLICATION_JSON())->unmarshal($json_, $type_);
    }

    /**
     * @param \Components\Io_Mimetype $mimeType_
     * @param mixed $object_
     *
     * @return string
     *
     * @throws \Components\Exception_NotSupported
     */
    public static function marshal(Io_Mimetype $mimeType_, $object_)
    {
      return Object_Marshaller::forMimetype($mimeType_)->marshal($object_);
    }

    /**
     * @param \Components\Io_Mimetype $mimeType_
     * @param string $data_
     * @param string $type_
     *
     * @return mixed
     *
     * @throws \Components\Exception_NotSupported
     */
    public static function unmarshal(Io_Mimetype $mimeType_, $data_, $type_)
    {
      return Object_Marshaller::forMimetype($mimeType_)->unmarshal($data_, $type_);
    }

    /**
     * Cache object - preferrable of type \Components\Value.
     *
     * @param string $key_
     * @param mixed $object_
     */
    public static function cache($key_, $object_)
    {
      if($object_ instanceof Value)
      {
        Cache::set("components/object:$key_", array(get_class($object_), $object_->value()));
      }
      else if(is_object($object_))
      {
        if(null===self::$m_mapper)
          self::$m_mapper=new Object_Mapper();

        Cache::set("components/object:$key_", array(get_class($object_), self::$m_mapper->dehydrate($object_)));
      }
      else
      {
        throw new Exception_NotImplemented('components/objects', 'Caching/Loading collections of objects is not implemented yet.');
      }
    }

    /**
     * Load cached object.
     *
     * @param string $key_
     *
     * @return null|mixed
     */
    public static function load($key_)
    {
      $value=Cache::get("components/object:$key_");

      if(false===$value)
        return null;

      $type=$value[0];

      if(method_exists($type, 'valueOf'))
      {
        if(is_array($value[1]))
          throw new Exception_NotImplemented('components/objects', 'Caching/Loading collections of objects is not implemented yet.');

        return $type::valueOf($value[1]);
      }

      if(null===self::$m_mapper)
        self::$m_mapper=new Object_Mapper();

      return self::$m_mapper->hydrateForType($type, $value[1]);
    }

    /**
     * Loads cached object for given type. Object will be initialized with
     * optional default value if not found in cache.
     *
     * @param string $key_
     * @param string $type_
     * @param mixed $default_
     *
     * @return null|mixed
     */
    public static function loadForType($key_, $type_, $default_=null)
    {
      $value=Cache::get("components/object:$key_");

      if(false===$value)
      {
        if(null===$default_)
          return null;

        $value=array(1=>$default_);
      }

      if(is_array($value[1]))
      {
        if(null===self::$m_mapper)
          self::$m_mapper=new Object_Mapper();

        return self::$m_mapper->hydrateForType($type_, $value[1]);
      }

      // TODO Scalar value currently implies that corresponding object type should implement Value - saves reflections, but its not safe ...
      return $type_::valueOf($value[1]);
    }

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


    // IMPLEMENTATION
    /**
    * @var \Components\Object_Mapper
    */
    private static $m_mapper;
    //--------------------------------------------------------------------------
  }
?>
