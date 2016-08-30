<?php

class Simi_Livechatzopim_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getDataConfig($tag){
		 $storeId = Mage::app()->getStore()->getId();
		 return Mage::getStoreConfig('livechatzopim/general/'.$tag,$storeId);
	}
}