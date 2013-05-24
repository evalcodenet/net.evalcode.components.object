<?php


namespace Components;


  /**
   * Objects
   *
   * @package net.evalcode.components
   * @subpackage object
   *
   * @author evalcode.net
   */
  class Objects
  {
    // STATIC ACCESSORS
    /**
     * @param mixed $object_
     * @param array|scalar $data_
     */
    public static function hydrate($object_, array $data_)
    {
      if(null===self::$m_mapper)
        self::$m_mapper=new Object_Mapper();

      return self::$m_mapper->hydrate($object_, $data_);
    }

    /**
     * @param array|scalar $data_
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
     * @return array|scalar
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
      return Object_Marshaller::forMimeType(Io_MimeType::APPLICATION_JSON())->marshal($object_);
    }

    /**
     * @param string $json_
     * @param string $type_
     *
     * @return mixed
     */
    public static function forJson($json_, $type_)
    {
      return Object_Marshaller::forMimeType(Io_MimeType::APPLICATION_JSON())->unmarshal($json_, $type_);
    }

    /**
     * @param \Components\Io_MimeType $mimeType_
     * @param mixed $object_
     *
     * @return string
     *
     * @throws \Components\Exception_NotSupported
     */
    public static function marshal(Io_MimeType $mimeType_, $object_)
    {
      return Object_Marshaller::forMimeType($mimeType_)->marshal($object_);
    }

    /**
     * @param \Components\Io_MimeType $mimeType_
     * @param string $data_
     * @param string $type_
     *
     * @return mixed
     *
     * @throws \Components\Exception_NotSupported
     */
    public static function unmarshal(Io_MimeType $mimeType_, $data_, $type_)
    {
      return Object_Marshaller::forMimeType($mimeType_)->unmarshal($data_, $type_);
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
