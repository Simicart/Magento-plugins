<?php

class Magestore_Storelocator_Model_Storevalue extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('storelocator/storevalue');
    }

    public function loadAttributeValue($storeLocatorId, $storeId, $attributeCode) {
        $attributeValue = $this->getCollection()
                ->addFieldToFilter('storelocator_id', $storeLocatorId)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('attribute_code', $attributeCode)
                ->getFirstItem();
        $this->setData('storelocator_id', $storeLocatorId)
                ->setData('store_id', $storeId)
                ->setData('attribute_code', $attributeCode);
        if ($attributeValue)
            $this->addData($attributeValue->getData())
                    ->setId($attributeValue->getId());
        return $this;
    }

}