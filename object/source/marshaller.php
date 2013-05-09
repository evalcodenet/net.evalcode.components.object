<?php


namespace Components;


  /**
   * Marshaller
   *
   * @package net.evalcode.components
   * @subpackage object
   *
   * @author evalcode.net
   */
  abstract class Marshaller
  {
    // STATIC ACCESSORS
    /**
     * @param Components\Io_MimeType $mimeType_
     *
     * @return Components\Marshaller
     */
    public static function forMimeType(Io_MimeType $mimeType_)
    {
      if(false===isset(self::$m_marshallerTypes[$mimeType_->name()]))
      {
        throw new Exception_NotSupported('components/marshaller',
          sprintf('Given mimetype is not supported [mimetype: %s].', $mimeType_)
        );
      }

      if(false===isset(self::$m_marshallerInstances[$mimeType_->name()]))
      {
        $type=self::$m_marshallerTypes[$mimeType_->name()];
        self::$m_marshallerInstances[$mimeType_->name()]=new $type();
      }

      return self::$m_marshallerInstances[$mimeType_->name()];
    }

    /**
     * Register additional marshaller implementations.
     *
     * @param Io_MimeType $mimeType_
     * @param string $typeMarshaller_
     *
     * @throws Exception_IllegalArgument
     */
    public static function registerForMimeType(Io_MimeType $mimeType_, $typeMarshaller_)
    {
      if(!$typeMarshaller_ instanceof self)
      {
        throw new Exception_IllegalArgument('components/marshaller',
          'Passed type must extend from '.__CLASS__.'.'
        );
      }

      self::$m_marshallerTypes[$mimeType_->name()]=$typeMarshaller_;
    }

    /**
     * Resolve type of responsible serializer for given type of object.
     *
     * @param string $type_
     *
     * @return string
     *
     * @throws Exception_IllegalArgument
     */
    public static function getSerializerForType($type_)
    {
      if(false===isset(self::$m_serializerInstances[$type_]))
      {
        if(false===isset(self::$m_serializerTypes[$type_]))
        {
          throw new Exception_IllegalArgument('components/marshaller', sprintf(
            'Serialization of passed type is not supported [%s].', $type_
          ));
        }

        $impl=self::$m_serializerTypes[$type_];

        self::$m_serializerInstances[$type_]=new $impl();
      }

      return self::$m_serializerInstances[$type_];
    }

    /**
     * Resolve cached instance of responsible serializer
     * for given type of object.
     *
     * @param string $type_
     *
     * @return Components\Serializer
     */
    public static function getSerializerImplForType($type_)
    {
      if(false===isset(self::$m_serializerTypes[$type_]))
        return null;

      return self::$m_serializerTypes[$type_];
    }

    /**
     * Register additional serializer implementations.
     *
     * @param string $type_
     * @param "Components\Serializer" $serializer_
     */
    public static function registerSerializerImplForType($type_, $serializer_)
    {
      self::$m_serializerTypes[$type_]=$serializer_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param Components\Serializable $object_
     *
     * @return string
     */
    abstract public function marshal(Serializable $object_);
    /**
     * @param string $data_
     * @param "Components\Serializable" $type_
     *
     * @return Components\Serializable
     */
    abstract public function unmarshal($data_, /** @var Components\Serializable */ $type_);
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_marshallerTypes=array(
      Io_MimeType::APPLICATION_JSON=>'Components\\Marshaller_Json'
    );
    private static $m_marshallerInstances=array();
    private static $m_serializerTypes=array(
      Boolean::TYPE=>'Components\\Serializer_Primitive',
      Character::TYPE=>'Components\\Serializer_Primitive',
      Integer::TYPE=>'Components\\Serializer_Primitive',
      Float::TYPE=>'Components\\Serializer_Primitive',
      String::TYPE=>'Components\\Serializer_Primitive',
      HashMap::TYPE=>'Components\\Serializer_Map',
    );
    private static $m_serializerInstances=array();
    //--------------------------------------------------------------------------
  }
?>
