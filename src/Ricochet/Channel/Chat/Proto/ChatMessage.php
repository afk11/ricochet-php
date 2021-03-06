<?php
// DO NOT EDIT! Generated by Protobuf-PHP protoc plugin 0.9.4
// Source: Chat.proto
//   Date: 2016-08-05 18:35:13

namespace Ricochet\Channel\Chat\Proto {

  class ChatMessage extends \DrSlump\Protobuf\Message {

    /**  @var string */
    public $message_text = null;
    
    /**  @var int */
    public $message_id = null;
    
    /**  @var int */
    public $time_delta = null;
    

    /** @var \Closure[] */
    protected static $__extensions = array();

    public static function descriptor()
    {
      $descriptor = new \DrSlump\Protobuf\Descriptor(__CLASS__, 'Ricochet.Channel.Chat.ChatMessage');

      // REQUIRED STRING message_text = 1
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 1;
      $f->name      = "message_text";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_REQUIRED;
      $descriptor->addField($f);

      // OPTIONAL UINT32 message_id = 2
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 2;
      $f->name      = "message_id";
      $f->type      = \DrSlump\Protobuf::TYPE_UINT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL INT64 time_delta = 3
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 3;
      $f->name      = "time_delta";
      $f->type      = \DrSlump\Protobuf::TYPE_INT64;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      foreach (self::$__extensions as $cb) {
        $descriptor->addField($cb(), true);
      }

      return $descriptor;
    }

    /**
     * Check if <message_text> has a value
     *
     * @return boolean
     */
    public function hasMessageText(){
      return $this->_has(1);
    }
    
    /**
     * Clear <message_text> value
     *
     * @return \Ricochet\Channel\Chat\Proto\ChatMessage
     */
    public function clearMessageText(){
      return $this->_clear(1);
    }
    
    /**
     * Get <message_text> value
     *
     * @return string
     */
    public function getMessageText(){
      return $this->_get(1);
    }
    
    /**
     * Set <message_text> value
     *
     * @param string $value
     * @return \Ricochet\Channel\Chat\Proto\ChatMessage
     */
    public function setMessageText( $value){
      return $this->_set(1, $value);
    }
    
    /**
     * Check if <message_id> has a value
     *
     * @return boolean
     */
    public function hasMessageId(){
      return $this->_has(2);
    }
    
    /**
     * Clear <message_id> value
     *
     * @return \Ricochet\Channel\Chat\Proto\ChatMessage
     */
    public function clearMessageId(){
      return $this->_clear(2);
    }
    
    /**
     * Get <message_id> value
     *
     * @return int
     */
    public function getMessageId(){
      return $this->_get(2);
    }
    
    /**
     * Set <message_id> value
     *
     * @param int $value
     * @return \Ricochet\Channel\Chat\Proto\ChatMessage
     */
    public function setMessageId( $value){
      return $this->_set(2, $value);
    }
    
    /**
     * Check if <time_delta> has a value
     *
     * @return boolean
     */
    public function hasTimeDelta(){
      return $this->_has(3);
    }
    
    /**
     * Clear <time_delta> value
     *
     * @return \Ricochet\Channel\Chat\Proto\ChatMessage
     */
    public function clearTimeDelta(){
      return $this->_clear(3);
    }
    
    /**
     * Get <time_delta> value
     *
     * @return int
     */
    public function getTimeDelta(){
      return $this->_get(3);
    }
    
    /**
     * Set <time_delta> value
     *
     * @param int $value
     * @return \Ricochet\Channel\Chat\Proto\ChatMessage
     */
    public function setTimeDelta( $value){
      return $this->_set(3, $value);
    }
  }
}

