<?php
class Simi_Simidailydeal_Block_Adminhtml_Dailydeal_Renderer_Productprice extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){
        $store = Mage::app()->getStore();
        $product = Mage::getModel('catalog/product')->load($row->getProductId());
        //zend_debug::dump($product->getData());
        return $store->convertPrice($product->getPrice(),true);
    }
}