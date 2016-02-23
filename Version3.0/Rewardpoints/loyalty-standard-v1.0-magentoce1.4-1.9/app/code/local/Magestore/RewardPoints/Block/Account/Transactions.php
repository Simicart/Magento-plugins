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
 * Rewardpoints Block
 * 
 * @category    
 * @package     Rewardpoints
 * @author      Developer
 */
class Magestore_RewardPoints_Block_Account_Transactions extends Magestore_RewardPoints_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $collection = Mage::getResourceModel('rewardpoints/transaction_collection')
            ->addFieldToFilter('customer_id', $customerId);
        $collection->getSelect()->order('created_time DESC');
        $this->setCollection($collection);
    }
    
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'transactions_pager')
            ->setCollection($this->getCollection());
        $this->setChild('transactions_pager', $pager);
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('transactions_pager');
    }
}
