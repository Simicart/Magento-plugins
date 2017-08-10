<?php

class Simi_Simidailydeal_Block_Adminhtml_Dailydeal_Renderer_Save extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		return $row->getSave() . '%';
	}
}