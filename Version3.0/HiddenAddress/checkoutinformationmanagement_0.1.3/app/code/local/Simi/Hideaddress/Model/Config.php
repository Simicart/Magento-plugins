<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Hideaddress
 * @copyright   Copyright (c) 2012 
 * @license   
 */

/**
 * Hideaddress Model
 * 
 * @category    
 * @package     Hideaddress
 * @author      Developer
 */
class Simi_Hideaddress_Model_Config {

    public function toAddressArray() {
        return array(
           
            array('value' => 'company', 'label' => Mage::helper('core')->__('Company')),
            array('value' => 'street', 'label' => Mage::helper('core')->__('Street')),
            array('value' => 'country', 'label' => Mage::helper('core')->__('Country')),
            array('value' => 'state', 'label' => Mage::helper('core')->__('State')),
            array('value' => 'city', 'label' => Mage::helper('core')->__('City')),
            array('value' => 'zipcode', 'label' => Mage::helper('core')->__('ZipCode')),
            array('value' => 'telephone', 'label' => Mage::helper('core')->__('Telephone')),
            array('value' => 'fax', 'label' => Mage::helper('core')->__('Fax')),
            array('value' => 'prefix', 'label' => Mage::helper('core')->__('Prefix')),
           // array('value' => 'middlename', 'label' => Mage::helper('core')->__('Middlename')),
            array('value' => 'suffix', 'label' => Mage::helper('core')->__('Suffix')),
            array('value' => 'birthday', 'label' => Mage::helper('core')->__('Birthday')),
            array('value' => 'gender', 'label' => Mage::helper('core')->__('Gender')),
            array('value' => 'taxvat', 'label' => Mage::helper('core')->__('Taxvat')));
    }
    public function toRespondArrray(){
         return array(
           
            array('value' => 'company','respond'=>'company', 'label' => Mage::helper('core')->__('Company')),
            array('value' => 'street','respond'=>'street', 'label' => Mage::helper('core')->__('Street')),
            array('value' => 'country','respond'=>'country_code', 'label' => Mage::helper('core')->__('Country')),
            array('value' => 'state','respond'=>'state_code', 'label' => Mage::helper('core')->__('State')),
            array('value' => 'city','respond'=>'city', 'label' => Mage::helper('core')->__('City')),
            array('value' => 'zipcode','respond'=>'zip', 'label' => Mage::helper('core')->__('Zip Code')),
            array('value' => 'telephone','respond'=>'phone', 'label' => Mage::helper('core')->__('Phone')),
            array('value' => 'fax','respond'=>'fax', 'label' => Mage::helper('core')->__('fax')),
            array('value' => 'prefix','respond'=>'prefix', 'label' => Mage::helper('core')->__('Prefix')),
           // array('value' => 'middlename', 'label' => Mage::helper('core')->__('Middlename')),
            array('value' => 'suffix','respond'=>'suffix', 'label' => Mage::helper('core')->__('Suffix')),
            array('value' => 'birthday','respond'=>'year', 'label' => Mage::helper('core')->__('Birthday')),
            array('value' => 'gender','respond'=>'gender', 'label' => Mage::helper('core')->__('Gender')),
            array('value' => 'taxvat','respond'=>'taxvat', 'label' => Mage::helper('core')->__('Taxvat')));
    }

}