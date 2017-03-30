<?php
class Simi_Simidailydeal_Block_Adminhtml_Simidailydeal_Renderer_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
                $time=$row->getStartTime();
		return $time;
	}
}