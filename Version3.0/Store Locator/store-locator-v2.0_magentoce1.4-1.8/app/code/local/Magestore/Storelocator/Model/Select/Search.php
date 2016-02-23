<?php

class Magestore_Storelocator_Model_Select_Search
{
    public function toOptionArray()
    {
        return array(
            array('value'=>5, 'label'=>Mage::helper('storelocator')->__('None')),
            //array('value'=>0, 'label'=>Mage::helper('storelocator')->__('Store Name')),
            array('value'=>1, 'label'=>Mage::helper('storelocator')->__('Country')),
            array('value'=>2, 'label'=>Mage::helper('storelocator')->__('State/ Province')),
            array('value'=>3, 'label'=>Mage::helper('storelocator')->__('City')),
            array('value'=>4, 'label'=>Mage::helper('storelocator')->__('Zip Code')),
           
        );
    }
}