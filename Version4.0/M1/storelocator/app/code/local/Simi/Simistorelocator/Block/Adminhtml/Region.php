<?php
class Simi_Simistorelocator_Block_Adminhtml_Region extends Mage_Core_Block_Template
{
	public function getStore()
	{
		$collection = null;
		$id = $this->getRequest()->getParam('id');
		if($id)
		{
			$collection = Mage::getModel('simistorelocator/simistorelocator')->load($id);
		}
		return $collection;
	}
}
?>