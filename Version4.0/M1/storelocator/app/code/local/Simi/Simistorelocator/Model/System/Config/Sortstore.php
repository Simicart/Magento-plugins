<?php

class Simi_Simistorelocator_Model_System_Config_Sortstore {

    public function toOptionArray() {
        $options = array(
            array('value' => 'distance', 'label' => Mage::helper('simistorelocator')->__('Distance')),
            array('value' => 'alphabeta', 'label' => Mage::helper('simistorelocator')->__('Alphabetical order')),
        );
        return $options;
    }

}