<?php

class Simicart_Simihr_Block_Department_Development extends Mage_Core_Block_Template
{

	public function _prepareLayout()
	{ 	    
		return parent::_prepareLayout();
	}
	public function hello() {
		echo "string";
	}
	public function getDevelopmentJob($type)
	{
		$developmentId = Mage::getResourceModel('simicart_simihr/department_collection')->addFieldToFilter('name', 'development')->getData();
		if (isset($developmentId[0])) {
			$department_id = $developmentId[0]['id'];
			$jobCollection = Mage::getResourceModel('simicart_simihr/jobOffers_collection')->addFieldToSelect ('*')->addFieldToFilter('department_id', $developmentId)->addFieldToFilter($type, 1)->getData();
		}

		return $jobCollection;
	}
}