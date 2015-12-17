<?php

class Simi_Simipayuindia_Block_Simipayuindia extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('simipayuindia/simipayuindia.phtml');
        parent::_construct();
    }
}