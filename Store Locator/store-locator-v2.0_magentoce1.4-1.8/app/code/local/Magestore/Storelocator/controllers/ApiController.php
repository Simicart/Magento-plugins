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
 * @package 	Magestore_Storelocator
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Storelocator Index Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Storelocator
 * @author  	Magestore Developer
 */
class Magestore_Storelocator_ApiController extends Simi_Connector_Controller_Action {

    public function get_store_listAction() {
        $data = $this->getData();
        $information = Mage::getSingleton('storelocator/api')->getStoreList($data);
        $this->_printDataJson($information);
    }

    public function get_search_configAction() {
        $information = Mage::getSingleton('storelocator/api')->getSearchConfig();
        $this->_printDataJson($information);
    }
	
	public function get_search_config_iosAction(){
		$information = Mage::getSingleton('storelocator/api')->getSearchConfigIos();
        $this->_printDataJson($information);
	}
	
    public function get_tag_listAction() {
        $data = $this->getData();
        $information = Mage::getSingleton('storelocator/api')->getTagList($data);
        $this->_printDataJson($information);
    }

    public function get_country_listAction() {
        $information = Mage::getSingleton('storelocator/api')->getAllowedCountries();
        $this->_printDataJson($information);
    }
   
	public function get_store_list_mapAction(){
		$data = $this->getData();
        $information = Mage::getSingleton('storelocator/api')->getStoreByDistanceMap($data);
        $this->_printDataJson($information);
	}
	
	
    public function testAction() {
        $data = $this->getData();
        // Zend_debug::dump($data);die();
        Mage::getSingleton('storelocator/api')->getStoreBtDistance($data);
    }

}