<?php
/**
 * The event notification object
 * 
 * @author Jon Bomgardner
 * @copyright 2008 by SolaGratiaDesigns.com
 * @package Event
 */
class Jx_Event_Notification {
	
	private $_object;
	private $_eventName;
	private $_stop= false;
	private $_options = null;
	private $_returnMessage = null;
	private $_returnCode = 0;
	private $_returnData = null;
	
	public function __construct($object, $eventName, $options) {
		$this->_object = $object;
		$this->_eventName = $eventName;
		$this->_options = $options;
	}
	
	public function getObject() {
		return $this->_object;
	}
	
	public function getEventName(){
		return $this->_eventName;
	}
	
	public function stopEvent(){
		$this->_stop = true;
	}
	
	public function isEventStopped(){
		return $this->_stop;
	}
	
	public function getOptions(){
		return $this->_options;
	}
	
	public function setReturnMessage($message){
		$this->_returnMessage = $message;
		return $this;
	}
	
	public function getReturnMessage(){
		return $this->_returnMessage;
	}
	
	public function setReturnCode($code){
		$this->_returnCode = $code;
		return $this;
	}
	
	public function getReturnCode(){
		return $this->_returnCode;
	}
	
	public function setReturnData($data){
		$this->_returnData = $data;
		return $this;
	}
	
	public function getReturnData(){
		return $this->_returnData;
	}
	
	public function hasReturnData(){
		return (!empty($this->_returnData));
	}
	
	public function setReturnInfo($message = null, $code = 0) {
		return $this->setReturnMessage($message)->setReturnCode($code);
	}
}
