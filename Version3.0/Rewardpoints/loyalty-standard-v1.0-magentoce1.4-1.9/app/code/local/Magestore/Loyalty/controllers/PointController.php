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
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Loyalty Point Controller
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
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
