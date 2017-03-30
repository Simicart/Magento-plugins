<?php

class Simi_Simidailydeal_Model_Simidailydeal extends Mage_Core_Model_Abstract
{
	protected $_arrayfilter=null;
	protected $_simidailydealCollection=null;
    public function _construct(){
        parent::_construct();
        $this->_init('simidailydeal/simidailydeal');
    }

    /**
     * get collection simidailydeals active on current store
     *
     * @param 
     * @return Simi_Simidailydeal_Model_Simidailydeal $simidailydeals
     */
    public function getSimidailydeals(){
		if (is_null($this->_simidailydealCollection)) {
        $store=Mage::app()->getStore()->getStoreId();
        $simidailydeals=$this->getCollection()
                ->addFieldToFilter('status',3)
                ->addFieldToFilter('is_random',0)
                ->addFieldToFilter('store_id',$this->getArrayFilter($store))
                ->addFieldToFilter('product_id',array('nin'=>0));
				//->addFieldToFilter('close_time',array('nin'=>null));
		$this->_simidailydealCollection=$simidailydeals;
		}
        return $this->_simidailydealCollection;
    }

    /**
     * get collection products by store
     *
     * @param  string $store example '1,2,3'
     * @return Simi_Catalog_Model_Product $products
     */
    public function getLoadedProductCollection($store=null){
        
        $simidailydeals=$this->getCollection()
            ->addFieldToFilter('status',3)
            ->addFieldToFilter('is_random',0)
            ->addFieldToFilter('product_id',array('nin'=>0));
        if ($store!=0)
            $simidailydeals->addFieldToFilter('store_id',$this->getArrayFilter($store));
        $productIds=array();
        foreach ($simidailydeals as $simidailydeal) {
            if($simidailydeal->getQuantity()>$simidailydeal->getSold())
                $productIds[]=$simidailydeal->getProductId();
        }
        $products=Mage::getModel('catalog/product')->getCollection()
                ->addFieldToFilter('entity_id',array('in'=>$productIds))
                ->addAttributeToSelect('*');
        return $products;
    }

    /**
     * get collection random products display on sidebar
     *
     * @param  
     * @return Simi_Catalog_Model_Product $productsidebars
     */
    public function getSidebarProductCollection(){
        $current_product=Mage::registry('current_product');
		$productIds=$this->getSimidailydeals()->getAllProductIds();
		
		if ($current_product){
			if(array_search($current_product->getId(),$productIds)===false){
			}else{
				unset($productIds[array_search($current_product->getId(),$productIds)]);
			}
		}
        $productsidebar=array();
		
        $limit=Mage::getStoreConfig('simidailydeal/sidebar/number_deal');
        if($limit > count($productIds))$limit = count($productIds);
        $rand_keys = array_rand($productIds, $limit);
        if ($limit==1){
            $productsidebar[]=$productIds[$rand_keys];
        }elseif ($limit>1) {
            foreach ($rand_keys as $rand_key) {
                $productsidebar[]=$productIds[$rand_key];
            }
        }
		
        $productsidebars=Mage::getModel('catalog/product')->getCollection()
                    ->addFieldToFilter('entity_id',array('in'=>$productsidebar))
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productsidebars);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($productsidebars);
        return $productsidebars;
    }

    /**
     * get simidailydeal by product_id
     *
     * @param  int $productId
     * @return array $simidailydeal
     */
    public function getSimidailydealByProduct($productId){
        $simidailydeal=$this->getSimidailydeals()       
                ->addFieldToFilter('product_id',$productId);
		if($simidailydeal->getSize())
            return $simidailydeal->getFirstItem();
        return Mage::getModel('simidailydeal/simidailydeal');
    }


    /**
     * 
     *
     * @param  Simi_Simidailydeal_Model_Simidailydeal $simidailydeal
     * @return boolean example 'true'
     */
    public function isExitDealContainProduct($simidailydeal) {
		if(!$simidailydeal->getIsRandom())return false;
        $simidailydeals=$this->getCollection()
            ->addFieldToFilter('status',3)
            ->addFieldToFilter('is_random',0)
            ->addFieldToFilter('product_id',$simidailydeal->getProductId());
        if ($simidailydeal->getStoreId()!=0)
            $simidailydeals->addFieldToFilter('store_id',$this->getArrayFilter($simidailydeal->getStoreId()));
        if ($simidailydeals->getSize()>1) {
            return true;
        }else if ($simidailydeals->getSize()==1){
            $simidailydealcheck=$simidailydeals->getFirstItem();
            if ($simidailydealcheck->getId() !=$simidailydeal->getId()) return true;
        } 
        return false;
    }

    /**
     * get limit of product on order
     *
     * @param  int $simidailydealId
     * @return int $temp
     */
    public function getLimit($simidailydealId){
        $quantity=$this->load($simidailydealId)->getQuantity();
        $collection1 = Mage::getResourceModel('sales/order_collection')
                    ->addFieldToFilter('simidailydeals',array('finset'=>$simidailydealId));
        $temp=$quantity-$collection1->getSize();
        return $temp;
    }

    /**
     * get array to filter
     *
     * @param  string $store example '1,2,3'
     * @return array $array
     */
    public function getArrayFilter($store){
		if (is_null($this->_arrayfilter)) {
        $arr=explode(',',$store);
        $array=array();
        if($store!=0)
        foreach($arr as $a) {
            $array[]=array('finset'=>$a);
        }
        $array[]=array('finset'=>0);
		$this->_arrayfilter=$array;
		}
        return $this->_arrayfilter;
    }
}