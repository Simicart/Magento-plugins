<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Themeone
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Themeone Controller
 * 
 * @category    
 * @package     Themeone
 * @author      Developer
 */
class Simi_Themeone_SpotproductrewriteController extends Simi_Spotproduct_IndexController
{
   public function indexAction() {
        $this->loadLayout();
 
        $this->renderLayout();
    }
   
    public function get_spot_productsAction(){
             $status=Mage::getStoreConfig('themeone/general/enable');            
             if($status) {
                 $information = Mage::getModel('themeone/spotproduct_spot')->getDisableModule();
                 $this->_printDataJson($information);       
             }
             else{
                 parent::get_spot_productsAction();
             }
    }
	
	public function get_spot_products_v2Action(){
         $status=Mage::getStoreConfig('themeone/general/enable');            
             if($status) {
                 $information = Mage::getModel('themeone/spotproduct_spot')->getDisableModule();
                 $this->_printDataJson($information);       
             }
             else{
                 parent::get_spot_products_v2Action();
             }       
    }
}