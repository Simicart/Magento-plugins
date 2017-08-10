<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/4/17
 * Time: 10:19 AM
 */
class Simi_Simidailydeal_Model_Simiobserver
{

    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }


    public function simiSimiconnectorModelServerInitialize($observer)
    {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'simidailydeals') {
            $observerObjectData['module'] = 'simidailydeal';
        }
        $observerObject->setData($observerObjectData);
    }

    public function SimiconnectorModelApiProductsShowAfter($observer){
        $product = $observer->getObject();
        $data = $product->detail_info;
        $deal = Mage::getModel('simidailydeal/dailydeal')->getDealByProduct($data['product']['entity_id'])->getData();
        if ($deal){
            $deal['title'] = Mage::helper('simidailydeal')->getDailydealTitle($deal['title'],$deal['product_name'],$deal['save']);
            $deal['deal_price'] = Mage::app()->getStore()->convertPrice($deal['deal_price']);

            $deal_time = Mage::getModel('core/date')->timestamp($deal['close_time'])-Mage::getModel('core/date')->timestamp($deal['start_time']);
            $deal['deal_time'] = $deal_time;
            $deal['time_left'] = (Mage::getModel('core/date')->timestamp($deal['close_time']) - Mage::getModel('core/date')->timestamp(time()));
            $data['product']['dailydeal'] = $deal;
        }
        $product->detail_info = $data;
    }
}