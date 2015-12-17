<?php


class Simi_Simipayuindia_Model_Modes
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'Y', 'label' => 'Demo Mode'),
            array('value' => '', 'label' => 'Production Mode'),
        );
    }
}