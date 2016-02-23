<?php

class Magestore_Storelocator_Model_Mysql4_Image extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('storelocator/image', 'image_id');
	}
}