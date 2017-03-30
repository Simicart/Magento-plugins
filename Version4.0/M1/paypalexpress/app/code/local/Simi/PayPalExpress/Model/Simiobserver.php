<?php

class Simi_PayPalExpress_Model_Simiobserver {
    /*
     * simiconnector 4.0 
     */

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('paypal_express', 3);
        return;
    }

    public function preventOrderPlacing($observer) {
        $orderApiModel = $observer->getObject();
        if ((Mage::getSingleton('checkout/type_onepage')->getQuote()->getPayment()->getMethod()) && (Mage::getSingleton('checkout/type_onepage')->getQuote()->getPayment()->getMethodInstance()->getCode() == 'paypal_express')) {
            $paymentRedirect = Mage::getSingleton('checkout/type_onepage')->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($paymentRedirect && $paymentRedirect != '') {
                $orderApiModel->order_placed_info = array(
                    //'payment_redirect_url' => $paymentRedirect,
                    'payment_redirect' => 1,
                    'payment_method' => 'paypal_express'
                );
            }
            $orderApiModel->place_order = FALSE;
        }
    }

    public function connectorConfigGetPluginsReturnPaypalExpress($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();   
        if ($observerObjectData['resource'] == 'ppexpressapis') {
            $observerObjectData['module'] = 'paypalexpress';
        }   
        $observerObject->setData($observerObjectData);
    }

    public function getStoreviewInfoAter($observer) {
        $observerObject = $observer->getObject();
        $storeviewData = $observerObject->storeviewInfo;  
        if ((int) Mage::getStoreConfig('paypalexpress/general/enable_app') != 0)
        $storeviewData['paypal_express_config'] = array(
            'show_on_product_detail'=>Mage::getStoreConfig('paypalexpress/general/product_detail'),
            'show_on_cart'=>Mage::getStoreConfig('paypalexpress/general/cart'),
        );
        $observerObject->storeviewInfo = $storeviewData;
    }
	
	
	
	public function orderPlacedAfter($observer) {
        $connectorModule = Mage::app()->getRequest()->getControllerModule();
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$pos = strpos($currentUrl, 'ppexpressapis/place');
		if ($pos === false) {
           return;
        }
        $orderId = $observer->getEvent()->getOrder()->getId();
		$newTransaction = Mage::getModel('simiconnector/appreport');
        $newTransaction->setOrderId($orderId);
        try {
            $newTransaction->save();
        } catch (Exception $exc) {
            
        }
    }

    /*
     * end 4.0 events hanlding
     */
}
