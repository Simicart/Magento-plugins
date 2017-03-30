<?php

class Simi_Simidailydeal_Block_Simidailydeal extends Mage_Catalog_Block_Product_List

{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    /**
     * get collection simidailydeal active
     *
     * @param 
     * @return Simi_Simidailydeal_Model_Simidailydeal $simidailydeals
     */
    public function getSimidailydeals(){
        $simidailydeals=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydeals();
        return $simidailydeals;
    }

    /**
     * get simidailydeal by product_id
     *
     * @param int $productId
     * @return Simi_Simidailydeal_Model_Simidailydeal $simidailydeal
     */
    public function getSimidailydealByProduct($productId){
        $simidailydeal=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydealByProduct($productId);
        return $simidailydeal;
    }

    /**
     * get collection product from collection simidailydeals active
     *
     * @param 
     * @return Simi_Catalog_Model_Product $this->_productCollection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $productIds=array();
            $simidailydeals=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydeals();
            foreach ($simidailydeals as $simidailydeal) {
                if($simidailydeal->getQuantity()>$simidailydeal->getSold())
                $productIds[]=$simidailydeal->getProductId();
            }

			
			$this->_productCollection = Mage::getResourceModel('catalog/product_collection')
											->setStoreId($this->getStoreId())
											->addFieldToFilter('entity_id',array('in'=>$productIds))
											->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
											->addMinimalPrice()
											->addTaxPercents()
											->addStoreFilter()
											;
								
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($this->_productCollection);						
        											
		}
        return $this->_productCollection;
    }
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->_getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }
}