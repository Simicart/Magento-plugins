<?php

class Simi_Simidailydeal_Block_Productsimidailydeal extends Mage_Core_Block_Template

{
    public function getProduct() {
		return Mage::registry('current_product');
	}
    public function getSimidailydealByProduct($productId){
		if(!Mage::registry('is_random_simidailydeal'))
    	Mage::helper('simidailydeal')->updateSimidailydealStatus();
        $simidailydeal=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydealByProduct($productId);
        return $simidailydeal;
    }
}