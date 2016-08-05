<?php
// DO NOT EDIT! Generated by Protobuf-PHP protoc plugin 0.9.4
// Source: Control.proto
//   Date: 2016-08-04 01:45:57

namespace Ricochet\Channel\Control\Proto {

  class Packet extends \DrSlump\Protobuf\Message {

    /**  @var \Ricochet\Channel\Control\Proto\OpenChannel */
    public $open_channel = null;
    
    /**  @var \Ricochet\Channel\Control\Proto\ChannelResult */
    public $channel_result = null;
    
    /**  @var \Ricochet\Channel\Control\Proto\KeepAlive */
    public $keep_alive = null;
    
    /**  @var \Ricochet\Channel\Control\Proto\EnableFeatures */
    public $enable_features = null;
    
    /**  @var \Ricochet\Channel\Control\Proto\FeaturesEnabled */
    public $features_enabled = null;
    

    /** @var \Closure[] */
    protected static $__extensions = array();

    public static function descriptor()
    {
      $descriptor = new \DrSlump\Protobuf\Descriptor(__CLASS__, 'Ricochet.Channel.Control.Packet');

      // OPTIONAL MESSAGE open_channel = 1
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 1;
      $f->name      = "open_channel";
      $f->type      = \DrSlump\Protobuf::TYPE_MESSAGE;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $f->reference = '\Ricochet\Channel\Control\Proto\OpenChannel';
      $descriptor->addField($f);

      // OPTIONAL MESSAGE channel_result = 2
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 2;
      $f->name      = "channel_result";
      $f->type      = \DrSlump\Protobuf::TYPE_MESSAGE;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $f->reference = '\Ricochet\Channel\Control\Proto\ChannelResult';
      $descriptor->addField($f);

      // OPTIONAL MESSAGE keep_alive = 3
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 3;
      $f->name      = "keep_alive";
      $f->type      = \DrSlump\Protobuf::TYPE_MESSAGE;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $f->reference = '\Ricochet\Channel\Control\Proto\KeepAlive';
      $descriptor->addField($f);

      // OPTIONAL MESSAGE enable_features = 4
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 4;
      $f->name      = "enable_features";
      $f->type      = \DrSlump\Protobuf::TYPE_MESSAGE;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $f->reference = '\Ricochet\Channel\Control\Proto\EnableFeatures';
      $descriptor->addField($f);

      // OPTIONAL MESSAGE features_enabled = 5
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 5;
      $f->name      = "features_enabled";
      $f->type      = \DrSlump\Protobuf::TYPE_MESSAGE;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $f->reference = '\Ricochet\Channel\Control\Proto\FeaturesEnabled';
      $descriptor->addField($f);

      foreach (self::$__extensions as $cb) {
        $descriptor->addField($cb(), true);
      }

      return $descriptor;
    }

    /**
     * Check if <open_channel> has a value
     *
     * @return boolean
     */
    public function hasOpenChannel(){
      return $this->_has(1);
    }
    
    /**
     * Clear <open_channel> value
     *
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function clearOpenChannel(){
      return $this->_clear(1);
    }
    
    /**
     * Get <open_channel> value
     *
     * @return \Ricochet\Channel\Control\Proto\OpenChannel
     */
    public function getOpenChannel(){
      return $this->_get(1);
    }
    
    /**
     * Set <open_channel> value
     *
     * @param \Ricochet\Channel\Control\Proto\OpenChannel $value
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function setOpenChannel(\Ricochet\Channel\Control\Proto\OpenChannel $value){
      return $this->_set(1, $value);
    }
    
    /**
     * Check if <channel_result> has a value
     *
     * @return boolean
     */
    public function hasChannelResult(){
      return $this->_has(2);
    }
    
    /**
     * Clear <channel_result> value
     *
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function clearChannelResult(){
      return $this->_clear(2);
    }
    
    /**
     * Get <channel_result> value
     *
     * @return \Ricochet\Channel\Control\Proto\ChannelResult
     */
    public function getChannelResult(){
      return $this->_get(2);
    }
    
    /**
     * Set <channel_result> value
     *
     * @param \Ricochet\Channel\Control\Proto\ChannelResult $value
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function setChannelResult(\Ricochet\Channel\Control\Proto\ChannelResult $value){
      return $this->_set(2, $value);
    }
    
    /**
     * Check if <keep_alive> has a value
     *
     * @return boolean
     */
    public function hasKeepAlive(){
      return $this->_has(3);
    }
    
    /**
     * Clear <keep_alive> value
     *
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function clearKeepAlive(){
      return $this->_clear(3);
    }
    
    /**
     * Get <keep_alive> value
     *
     * @return \Ricochet\Channel\Control\Proto\KeepAlive
     */
    public function getKeepAlive(){
      return $this->_get(3);
    }
    
    /**
     * Set <keep_alive> value
     *
     * @param \Ricochet\Channel\Control\Proto\KeepAlive $value
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function setKeepAlive(\Ricochet\Channel\Control\Proto\KeepAlive $value){
      return $this->_set(3, $value);
    }
    
    /**
     * Check if <enable_features> has a value
     *
     * @return boolean
     */
    public function hasEnableFeatures(){
      return $this->_has(4);
    }
    
    /**
     * Clear <enable_features> value
     *
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function clearEnableFeatures(){
      return $this->_clear(4);
    }
    
    /**
     * Get <enable_features> value
     *
     * @return \Ricochet\Channel\Control\Proto\EnableFeatures
     */
    public function getEnableFeatures(){
      return $this->_get(4);
    }
    
    /**
     * Set <enable_features> value
     *
     * @param \Ricochet\Channel\Control\Proto\EnableFeatures $value
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function setEnableFeatures(\Ricochet\Channel\Control\Proto\EnableFeatures $value){
      return $this->_set(4, $value);
    }
    
    /**
     * Check if <features_enabled> has a value
     *
     * @return boolean
     */
    public function hasFeaturesEnabled(){
      return $this->_has(5);
    }
    
    /**
     * Clear <features_enabled> value
     *
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function clearFeaturesEnabled(){
      return $this->_clear(5);
    }
    
    /**
     * Get <features_enabled> value
     *
     * @return \Ricochet\Channel\Control\Proto\FeaturesEnabled
     */
    public function getFeaturesEnabled(){
      return $this->_get(5);
    }
    
    /**
     * Set <features_enabled> value
     *
     * @param \Ricochet\Channel\Control\Proto\FeaturesEnabled $value
     * @return \Ricochet\Channel\Control\Proto\Packet
     */
    public function setFeaturesEnabled(\Ricochet\Channel\Control\Proto\FeaturesEnabled $value){
      return $this->_set(5, $value);
    }
  }
}
