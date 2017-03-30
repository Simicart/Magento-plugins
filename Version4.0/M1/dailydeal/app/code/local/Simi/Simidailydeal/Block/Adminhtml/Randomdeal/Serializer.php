<?php

class Simi_Simidailydeal_Block_Adminhtml_Randomdeal_Serializer 
		extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('simidailydeal/serializer.phtml');
		return $this;
	}
	
	public function initSerializerBlock($gridName,$hiddenInputName)
	{
		$grid = $this->getLayout()->getBlock($gridName);
        $this->setGridBlock($grid)
                 ->setInputElementName($hiddenInputName);
	}
}