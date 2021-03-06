<?php

/**
 * Simi
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Simi.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simi.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/**
 * Simirewardpoints Transaction Resource Collection Model
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Mysql4_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('simirewardpoints/transaction');
    }

    /**
     * add availabel filter for transactions collection
     * 
     * @return Simi_Simirewardpoints_Model_Mysql4_Transaction_Collection
     */
    public function addAvailableBalanceFilter() {
        $this->getSelect()->where('point_amount > point_used');
        return $this;
    }

    /**
     * get total by field of this collection
     * 
     * @param string $field
     * @return number
     */
    public function getFieldTotal($field = 'point_amount') {
        $this->_renderFilters();

        $sumSelect = clone $this->getSelect();
        $sumSelect->reset(Zend_Db_Select::ORDER);
        $sumSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $sumSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $sumSelect->reset(Zend_Db_Select::COLUMNS);

        $sumSelect->columns("SUM(`$field`)");

        return $this->getConnection()->fetchOne($sumSelect, $this->_bindParams);
    }

    public function getSelectCountSql() {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        if (count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
            $countSelect->columns("COUNT(DISTINCT " . implode(", ", $group) . ")");
        } else {
            $countSelect->columns('COUNT(*)');
        }
        return $countSelect;
    }

}
