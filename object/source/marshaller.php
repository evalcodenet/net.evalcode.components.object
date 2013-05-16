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
      Io_MimeType::APPLICATION_JSON=>'Components\\Marshaller_Json'
    );
    private static $m_map=array(
      HashMap::TYPE=>'Components\\Marshaller::mapHashmap',
      HashMap::TYPE_NATIVE=>'Components\\Marshaller::mapArray'
    );
    private static $m_unmap=array(
      HashMap::TYPE=>'Components\\Marshaller::unmapHashmap',
      HashMap::TYPE_NATIVE=>'Components\\Marshaller::unmapArray'
    );
    private static $m_marshallerInstances=array();
    //-----


    protected function propertyMap($type_)
    {
      if($map=Cache::get('components/marshaller/json/map/'.md5($type_)))
        return $map;

      $annotations=Annotations::get($type_);

      $map=array();
      foreach($annotations->getPropertyAnnotations() as $propertyName=>$propertyAnnotations)
      {
        $property=array();
        foreach($propertyAnnotations as $annotation)
        {
          if($annotation instanceof Annotation_Type)
          {
            if(false===strpos($annotation->value, '|'))
            {
              $property['type']=$annotation->value;
              if(Primitive::isNative($property['type']))
                $property['type']=Primitive::asBoxed($property['type']);
            }
            else
            {
              $chunks=explode('|', $annotation->value);
              if(HashMap::TYPE_NATIVE===Primitive::asNative(ltrim(reset($chunks), '\\')))
              {
                $property['type']=HashMap::TYPE;
                $property['args']=ltrim(end($chunks), '\\');
                if(Primitive::isNative($property['args']))
                  $property['args']=Primitive::asBoxed($property['args']);
              }
              else
              {
                $property['type']=reset($chunks);
                if(Primitive::isNative($property['type']))
                  $property['type']=Primitive::asBoxed($property['type']);
              }
            }
          }

          if($annotation instanceof Annotation_Name)
            $property['name']=$annotation->value;
          else
            $property['name']=$propertyName;
        }

        $map[$propertyName]=$property;
      }

      Cache::set('components/marshaller/json/map/'.md5($type_), $map);

      return $map;
    }
    //--------------------------------------------------------------------------
  }
?>
