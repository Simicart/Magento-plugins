<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simibarcode
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simibarcode Model
 * 
 * @category    
 * @package     Simibarcode
 * @author      Developer
 */
class Simi_Simibarcode_Model_Barcodetypes extends Varien_Object
{
    
    /**
     * get model option as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'code39', 'label'=>Mage::helper('simibarcode')->__('Code-39')),
            array('value' => 'code128', 'label'=>Mage::helper('simibarcode')->__('Code-128')),            
            array('value' => 'code25', 'label'=>Mage::helper('simibarcode')->__('Code-25')),
            array('value' => 'code25interleaved', 'label'=>Mage::helper('simibarcode')->__('Interleaved 2 of 5')),
            array('value' => 'ean13', 'label'=>Mage::helper('simibarcode')->__('Ean-13')),
            array('value' => 'ean2', 'label'=>Mage::helper('simibarcode')->__('Ean-2')),
            array('value' => 'ean5', 'label'=>Mage::helper('simibarcode')->__('Ean-5')),
            array('value' => 'ean8', 'label'=>Mage::helper('simibarcode')->__('Ean-8')),
            array('value' => 'identcode', 'label'=>Mage::helper('simibarcode')->__('Identcode')),
            array('value' => 'itf14', 'label'=>Mage::helper('simibarcode')->__('Itf14')),
            array('value' => 'leitcode', 'label'=>Mage::helper('simibarcode')->__('Leitcode')),
            array('value' => 'planet', 'label'=>Mage::helper('simibarcode')->__('Planet')),
            array('value' => 'postnet', 'label'=>Mage::helper('simibarcode')->__('Postnet')),
            array('value' => 'royalmail', 'label'=>Mage::helper('simibarcode')->__('Royalmail')),
            array('value' => 'upca', 'label'=>Mage::helper('simibarcode')->__('UPC-A')),
            array('value' => 'upce', 'label'=>Mage::helper('simibarcode')->__('UPC-E')),
           
        );
    }
    
  
}