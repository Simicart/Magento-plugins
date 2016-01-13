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
 * @package 	Magestore_Simifblogin
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Simifblogin Index Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Simifblogin
 * @author  	Magestore Developer
 */
class Simi_Simifblogin_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
	 
	protected $_data;

    public function preDispatch() {
        parent::preDispatch();
        $value = $this->getRequest()->getParam('data');
        $this->praseJsonToData($value);
    }
	public function convertToJson($data) {
        $this->setData($data);     
        $this->_data = $this->getData();
        return Mage::helper('core')->jsonEncode($this->_data);
    }
	
	public function praseJsonToData($json) {
        $data = json_decode($json);
        $this->setData($data);        
        $this->_data = $this->getData();
    }
	
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }   
	
	public function getData() {
        return $this->_data;
    }

    public function setData($data) {
        $this->_data = $data;
    }
	
	
	public function checkInstallAction(){
		echo "1";
		exit();
   }
   
   public function _printDataJson($data) {
        echo $this->convertToJson($data);
        header("Content-Type: application/json");
        exit();
    }
	
    public function loginAction() {		
        $data = $this->getData();				
        $information = Mage::getModel('simifblogin/fblogin')->login($data);
		// Zend_debug::dump($information);die();
        $this->_printDataJson($information);
    }
		
}