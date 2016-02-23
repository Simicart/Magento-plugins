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
abstract class Magestore_RewardPoints_Model_Action_Abstract extends Varien_Object
{
    /**
     * Action Code
     * 
     * @var string
     */
    protected $_code = null;
    
    /**
     * get action code
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }
    
    /**
     * set action code
     * 
     * @param string $value
     * @return Magestore_RewardPoints_Model_Action_Abstract
     */
    public function setCode($value)
    {
        $this->_code = $value;
        return $this;
    }
    
    /**
     * get HTML Title for action depend on current transaction
     * 
     * @param Magestore_RewardPoints_Model_Transaction $transaction
     * @return string
     */
    public function getTitleHtml($transaction = null)
    {
        return $this->getTitle();
    }
    
    /**
     * prepare data of action to storage on transactions
     * the array that returned from function $action->getData('transaction_data')
     * will be setted to transaction model
     * 
     * @return Magestore_RewardPoints_Model_Action_Abstract
     */
    public function prepareTransaction()
    {
        return $this;
    }
    
    /**
     * Calculate Expiration Date for transaction
     * 
     * @param int $days Days to be expired
     * @return null|string
     */
    public function getExpirationDate($days = 0)
    {
        if ($days <= 0) {
            return null;
        }
        $timestamp = time() + $days * 86400;
        return date('Y-m-d H:i:s', $timestamp);
    }
}
