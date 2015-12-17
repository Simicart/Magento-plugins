<?php

class Magestore_Productlabel_Model_Indexer_Productlabel extends Mage_Index_Model_Indexer_Abstract {

    /**
     * Data key for matching result to be saved in
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        )
    );
    // var to protect multiple runs
    protected $_registered = false;
    protected $_processed = false;
    protected $_categoryId = 0;
    protected $_productIds = array();

    /**
     * not sure why this is required.
     * _registerEvent is only called if this function is included.
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event) {
        return Mage::getModel('catalog/category_indexer_product')->matchEvent($event);
    }

    public function getName() {
        return Mage::helper('productlabel')->__('Product Label Flat Data');
    }

    public function getDescription() {
        return Mage::helper('productlabel')->__('Refresh Product Label Flat Data');
    }

    protected function _registerEvent(Mage_Index_Model_Event $event) {
        // if event was already registered once, then no need to register again.
        if ($this->_registered)
            return $this;

        $entity = $event->getEntity();
        switch ($entity) {
//            case Mage_Catalog_Model_Product::ENTITY:
//                $this->_registerProductEvent($event);
//                break;

            case Mage_Catalog_Model_Category::ENTITY:
                $this->_registerCategoryEvent($event);
                break;

            case Mage_Catalog_Model_Convert_Adapter_Product::ENTITY:
                $event->addNewData('produclabel_indexer_reindex_all', true);
                break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
        $this->_registered = true;
        return $this;
    }

    /**
     * Register event data during product save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerProductEvent(Mage_Index_Model_Event $event) {
        $eventType = $event->getType();
        if ($eventType == Mage_Index_Model_Event::TYPE_SAVE) {
//            $process = $event->getProcess();
//            $this->_productIds = $event->getDataObject()->getData('product_ids');
//            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
           $this->reindexAll();
        }
    }

    /**
     * Register event data during category save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCategoryEvent(Mage_Index_Model_Event $event) {
        $category = $event->getDataObject();
        /**
         * Check if product categories data was changed
         * Check if category has another affected category ids (category move result)
         */
        if ($category->getIsChangedProductList() || $category->getAffectedCategoryIds()) {
            $process = $event->getProcess();
            $this->_categoryId = $event->getDataObject()->getData('entity_id');
            // $this->flagIndexRequired($this->_categoryId, 'categories');

            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }

    protected function _processEvent(Mage_Index_Model_Event $event) {
        // process index event
        if (!$this->_processed) {
            $this->_processed = true;
        }
    }

    public function reindexAll() {
        // reindex all data
        Mage::getModel('productlabel/productlabel')->applyAll();
    }

}
