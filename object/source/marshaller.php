<?php


namespace Components;


  /**
   * Object_Marshaller
   *
   * @package net.evalcode.components
   * @subpackage object
   *
   * @author evalcode.net
   */
  abstract class Object_Marshaller
  {
    // STATIC ACCESSORS
    /**
     * @param \Components\Io_MimeType $mimeType_
     *
     * @return \Components\Object_Marshaller
     */
    public static function forMimeType(Io_MimeType $mimeType_)
    {
      if(false===isset(self::$m_marshallerTypes[$mimeType_->name()]))
      {
        throw new Exception_NotSupported('object/marshaller',
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
        throw new Exception_IllegalArgument('object/marshaller',
          'Passed type must extend from '.__CLASS__.'.'
        );
      }

      self::$m_marshallerTypes[$mimeType_->name()]=$typeMarshaller_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param mixed $object_
     *
     * @return string
     */
    abstract public function marshal($object_);

    /**
     * @param string $data_
     * @param string $type_
     *
     * @return mixed
     */
    abstract public function unmarshal($data_, $type_);
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_marshallerTypes=array(
      Io_MimeType::APPLICATION_JSON=>'Components\\Object_Marshaller_Json'
    );
    private static $m_marshallerInstances=array();
    //--------------------------------------------------------------------------
  }
?>
