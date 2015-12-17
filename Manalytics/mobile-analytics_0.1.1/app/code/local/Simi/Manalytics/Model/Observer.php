<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Manalytics
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Manalytics Observer Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Manalytics
 * @author  	Magestore Developer
 */
class Simi_Manalytics_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Manalytics_Model_Observer
     */
    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

}
