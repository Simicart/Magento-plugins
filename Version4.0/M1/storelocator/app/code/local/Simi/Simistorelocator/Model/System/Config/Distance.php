<?php

class Simi_Simistorelocator_Model_System_Config_Distance {

    public function toOptionArray() {
        $options = array(
            array('value' => 'km', 'label' => Mage::helper('simistorelocator')->__('Kilometers')),
            array('value' => 'mi', 'label' => Mage::helper('simistorelocator')->__('Miles')),
        );
        return $options;
    }

}