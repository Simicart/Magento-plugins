<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Rewardpoints
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Rewardpoints Model
 * 
 * @category    
 * @package     Rewardpoints
 * @author      Developer
 */
class Magestore_RewardPoints_Model_Customer extends Mage_Core_Model_Abstract
{
    /**
     * Redefine event Prefix, event object
     * 
     * @var string
     */
    protected $_eventPrefix = 'rewardpoints_customer';
    protected $_eventObject = 'rewardpoints_customer';
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/customer');
    }
    
    /**
     * Get Customer Model
     * 
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->hasData('customer')) {
            $this->setData('customer',
                Mage::getModel('customer/customer')->load($this->getData('customer_id'))
            );
        }
        return $this->getData('customer');
    }
}
