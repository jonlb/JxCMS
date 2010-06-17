<?php
/**
 * The event system
 * 
 * @author Jon Bomgardner
 * @copyright 2008 by SolaGratiaDesigns.com
 * @package Event
 */
class Jx_Event {
	
	/**
	 * $_observers - holds all of the registered observers
	 */
	private static $_observers = array();
	
	/**
	 * addObserver() - adds observers to the $_observer array.  
	 * Only 1 of each class of Observer is allowed.
	 * 
	 * @param array $observer The observer to add. The array should be formatted
     *                      according to the docs for call_user_func()
     * @param string|array $event The event this observer is listening for. Can be an array of events.
	 */
	public static function addObserver($observer, $events) {

        if (!is_array($events)) {
            $events = array($events);
        }

        foreach ($events as $event) {
            self::$_observers[$event][] = $observer;
        }
	} 

	/**
	 * removeObserver() - removes an observer from the $_observer array
	 *
	 * @param array $observer The observer to remove. The array should be formatted
     *                      according to the docs for call_user_func()
     * @param string|array $event The event this observer is listening for. Can be an array of events. 
	 */
	public static function removeObserver($observer, $events) {

        if (!is_array($events)) {
            $events = array($events);
        }

        foreach ($events as $event) {
            if (isset(self::$_observers[$event]) && in_array($observer,self::$_observers[$event])) {
                
            }
        }

		if (is_object($className)) {
			$className = get_class($className);
		}
		unset(self::$_observers[$className]);
		if (permanent) {
			self::_removeObserverFromDatabase($className);
		}
	}
	
	/**
	 * isObserver() - checks to see if a class is registered as an observer
	 * 
	 * @param string $className The name of the class to check for
	 * @return boolean
	 */
	public static function isObserver($className) {
		if (is_object($className)) {
			$className = get_class($className);
		}
		return isset(self::$_observers[$className]);
	}
	
	/**
	 * post() - posts the event to each of the observers if it is observing the event 
	 * 			and has the proper method.
	 * 
	 * @param mixed $object
	 * @param string $event
	 * @return Event_Notification
	 */
	public static function post($object, $event, $options = null) {
		//normalize $options into an array
		if (is_string($options)){
			$options = array($options);
		} elseif (is_object($options)){
			$options = get_object_vars($options);
		} elseif (!is_array($options)){
			$options  = null;
		}
		
		$eventName = 'on'.ucfirst($event);
		
		//setup the notification object
		$notification = new Sgd_Event_Notification($object, $event, &$options);
		
		//loop through the observers and call the appropriate method if present
		foreach (self::$_observers as $o) {
			$observer = $o['object'];
			if (method_exists($observer,$eventName)) {
				$observer->{$eventName}($notification);
			} elseif (method_exists($observer, 'post')) {
				$observer->post($notification);
			}
			if ($notification->isEventStopped()) {
				break;
			}
		}
		
		return $notification;
	}
}
