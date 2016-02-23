<?php

class Magestore_Storelocator_Model_Mysql4_Storelocator_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_store_id = null;
    protected $_addedTable = array();
    
	public function _construct(){
		parent::_construct();
                if ($storeId = Mage::app()->getStore()->getId()) {
                    $this->setStoreId($storeId);
                }                
		$this->_init('storelocator/storelocator');
	}
        
        //use for multi store
        public function addFieldToFilter($field, $condition = null) 
        {
            $attributes = array(
                'name',
                'status',
                'description',
                'address',
                'city',
                'sort',
            );
            $storeId = $this->getStoreId();
            if (in_array($field, $attributes) && $storeId) {
                if (!in_array($field, $this->_addedTable)) {
                    $this->getSelect()
                        ->joinLeft(array($field => $this->getTable('storelocator/storevalue')),
                            "main_table.storelocator_id = $field.storelocator_id" .
                            " AND $field.store_id = $storeId" .
                            " AND $field.attribute_code = '$field'",
                            array()
                        );
                    $this->_addedTable[] = $field;
                }
                return parent::addFieldToFilter("IF($field.value IS NULL, main_table.$field, $field.value)", $condition);
            }
            if ($field == 'store_id') {
                $field = 'main_table.storelocator_id';
            }
            return parent::addFieldToFilter($field, $condition);
        }
        
        public function setStoreId($value){
		$this->_store_id = $value;
		return $this;
	}
	
	public function getStoreId(){
		return $this->_store_id;
	}
        //multi store
        protected function _afterLoad() {
            parent::_afterLoad();
            if ($storeId = $this->getStoreId()) {
                foreach ($this->_items as $item){
                    $item->setStoreId($storeId)->getMultiStoreValue();
                }
            }
            return $this;            
        }
        
}