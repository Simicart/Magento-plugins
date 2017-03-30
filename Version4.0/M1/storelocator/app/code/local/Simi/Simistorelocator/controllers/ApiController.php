<?php

class Simi_Simistorelocator_ApiController extends Simi_Connector_Controller_Action {

    public function get_store_listAction() {
        $data = $this->getData();
        $information = Mage::getSingleton('simistorelocator/api')->getStoreList($data);
        $this->_printDataJson($information);
    }

    public function get_search_configAction() {
        $information = Mage::getSingleton('simistorelocator/api')->getSearchConfig();
        $this->_printDataJson($information);
    }
	
	public function get_search_config_iosAction(){
		$information = Mage::getSingleton('simistorelocator/api')->getSearchConfigIos();
        $this->_printDataJson($information);
	}
	
    public function get_tag_listAction() {
        $data = $this->getData();
        $information = Mage::getSingleton('simistorelocator/api')->getTagList($data);
        $this->_printDataJson($information);
    }

    public function get_country_listAction() {
        $information = Mage::getSingleton('simistorelocator/api')->getAllowedCountries();
        $this->_printDataJson($information);
    }
   
	public function get_store_list_mapAction(){
		$data = $this->getData();
        $information = Mage::getSingleton('simistorelocator/api')->getStoreByDistanceMap($data);
        $this->_printDataJson($information);
	}
	
	
    public function testAction() {
        $data = $this->getData();
        // Zend_debug::dump($data);die();
        Mage::getSingleton('simistorelocator/api')->getStoreBtDistance($data);
    }

}