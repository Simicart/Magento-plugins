<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Themeone
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Themeone Controller
 * 
 * @category    
 * @package     Themeone
 * @author      Developer
 */
class Simi_Themeone_ConfigController extends Simi_Connector_ConfigController {

    public function get_bannerAction() {
         $status=Mage::getStoreConfig('themeone/general/enable');            
             if($status) {
        $information = Mage::getModel('themeone/config_app')->getBannerList();
        $this->_printDataJson($information);
        }
        else{
            parent::get_bannerAction();
        }
    }
    
    // public function get_pluginsAction() {
     
        // $status=Mage::getStoreConfig('themeone/general/enable');  
        // echo $status;
             // if($status) {
                 // $device_id = $this->getDeviceId();
        // $information = Mage::getModel('themeone/config_app')->getListPlugin($device_id);
        // $this->_printDataJson($information);
        // }
        // else{
            // parent::get_pluginsAction();
        // }
    // }

}
