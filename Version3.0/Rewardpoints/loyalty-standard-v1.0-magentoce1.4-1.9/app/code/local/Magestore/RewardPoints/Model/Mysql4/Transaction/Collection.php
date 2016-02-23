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
class Magestore_RewardPoints_Model_Mysql4_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/transaction');
    }
    
    /**
     * add availabel filter for transactions collection
     * 
     * @return Magestore_RewardPoints_Model_Mysql4_Transaction_Collection
     */
    public function addAvailableBalanceFilter()
    {
        $this->getSelect()->where('point_amount > point_used');
        return $this;
    }
    
    /**
     * get total by field of this collection
     * 
     * @param string $field
     * @return number
     */
    public function getFieldTotal($field = 'point_amount')
    {
        $this->_renderFilters();
        
        $sumSelect = clone $this->getSelect();
        $sumSelect->reset(Zend_Db_Select::ORDER);
        $sumSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $sumSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $sumSelect->reset(Zend_Db_Select::COLUMNS);
        
        $sumSelect->columns("SUM(`$field`)");
        
        return $this->getConnection()->fetchOne($sumSelect, $this->_bindParams);
    }
}
