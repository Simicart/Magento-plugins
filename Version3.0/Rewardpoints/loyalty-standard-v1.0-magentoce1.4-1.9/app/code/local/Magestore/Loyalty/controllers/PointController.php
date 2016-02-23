<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Loyalty
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Loyalty Controller
 * 
 * @category    
 * @package     Loyalty
 * @author      Developer
 */
class Magestore_Loyalty_PointController extends Simi_Connector_Controller_Action
{
    /**
     * Reward Points Home Info 
     */
    public function homeAction()
    {
    	$data = $this->getData();
        $information = Mage::getModel('loyalty/point')->getRewardInfo($data);
        $this->_printDataJson($information);
    }
    
    /**
     * Reward Points History
     */
    public function historyAction()
    {
    	$data = $this->getData();
    	$information = Mage::getModel('loyalty/point')->getHistory($data);
        $this->_printDataJson($information);
    }
    
    /**
     * Spend Points when checking out
     */
    public function spendAction()
    {
    	$data = $this->getData();
        $information = Mage::getModel('loyalty/point')->spendPoints($data);
        $this->_printDataJson($information);
    }
    
    /**
     * Save notification settings
     */
    public function settingsAction()
    {
    	$data = $this->getData();
        $information = Mage::getModel('loyalty/point')->saveSettings($data);
        $this->_printDataJson($information);
    }
}
