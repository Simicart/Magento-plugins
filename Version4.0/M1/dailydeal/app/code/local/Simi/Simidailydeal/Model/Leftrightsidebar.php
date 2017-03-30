<?php

class Simi_Simidailydeal_Model_Leftrightsidebar
{
    public function toOptionArray()
    {
        $options = array(
					array('value'=>1,'label'=> Mage::helper('simidailydeal')->__('Left sidebar')),
					array('value'=>2,'label'=> Mage::helper('simidailydeal')->__('Right sidebar')),
				);
		
		return $options;
    }
}