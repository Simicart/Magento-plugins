<?php

class Simi_Simidailydeal_Block_Adminhtml_Dailydeal_Serializer
	extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('simi/simidailydeal/serializer.phtml');

		return $this;
	}

	public function simiinitSerializerBlock($gridName, $hiddenInputName)
	{
		$grid = $this->getLayout()->getBlock($gridName);
		$this->setGridBlock($grid)
			->setInputElementName($hiddenInputName);
	}
}