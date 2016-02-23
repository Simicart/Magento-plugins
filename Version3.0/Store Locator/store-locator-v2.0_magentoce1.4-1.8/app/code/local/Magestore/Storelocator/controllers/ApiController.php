<?php

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