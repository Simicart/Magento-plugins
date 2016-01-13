<?php

class Simi_Simipayuindia_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $form = new Varien_Data_Form();
        $orderid = $this->getOrderId();
       
        $form->setAction(Mage::helper("simipayuindia")->getPayuCheckoutSharedUrl())
            ->setId('payucheckout_shared_checkout')
            ->setName('payucheckout_shared_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
    
        foreach (Mage::helper("simipayuindia")->getForm($orderid) as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to PayuCheckout in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("payucheckout_shared_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }


}