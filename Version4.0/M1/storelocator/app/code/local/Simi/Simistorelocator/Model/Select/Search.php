<?php

class Simi_Simistorelocator_Model_Select_Search
{
    public function toOptionArray()
    {
        return array(
            array('value'=>5, 'label'=>Mage::helper('simistorelocator')->__('None')),
            //array('value'=>0, 'label'=>Mage::helper('simistorelocator')->__('Store Name')),
            array('value'=>1, 'label'=>Mage::helper('simistorelocator')->__('Country')),
            array('value'=>2, 'label'=>Mage::helper('simistorelocator')->__('State/ Province')),
            array('value'=>3, 'label'=>Mage::helper('simistorelocator')->__('City')),
            array('value'=>4, 'label'=>Mage::helper('simistorelocator')->__('Zip Code')),
           
        );
    }
}