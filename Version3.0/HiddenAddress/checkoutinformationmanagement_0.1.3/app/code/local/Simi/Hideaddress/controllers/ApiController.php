<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Hideaddress
 * @copyright   Copyright (c) 2012 
 * @license   
 */

/**
 * Hideaddress Controller
 * 
 * @category    
 * @package     Hideaddress
 * @author      Developer
 */
class Simi_Hideaddress_ApiController extends Simi_Connector_Controller_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
     public function get_address_showAction(){
         // Mage::helper('debug')->save("thanh tung");
        $data=$this->getData(); 
        $information=Mage::getModel('hideaddress/hideaddress_show')->getAddressShow($data);
        $this->_printDataJson($information);
    }
     public function get_term_showAction(){
        $data=$this->getData(); 
        $information=Mage::getModel('hideaddress/hideaddress_show')->getTerm($data);
        $this->_printDataJson($information);
    }
}