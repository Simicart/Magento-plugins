<?php

class Magestore_Storelocator_Model_Observer
{
	/**
	 * process controller_action_predispatch event
	 *
	 * @return Magestore_Storelocator_Model_Observer
	 */
	public function controllerActionPredispatch($observer){
		$action = $observer->getEvent()->getControllerAction();
		return $this;
	}
}