<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     PayPalExpress
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * PayPalExpress Model
 * 
 * @category    
 * @package     PayPalExpress
 * @author      Developer
 */
class Simi_PayPalExpress_Model_Checkout extends Simi_Connector_Model_Checkout {

	//rewrite for PayPalExpress by Max - 2352015
    public function saveOrder($check_card, $data) {
        $information = null;
        $information = $this->statusSuccess();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array();
                $condition = $data->condition;
                if ($condition == "0") {
                    $information = $this->statusError(array($this->__('Please agree to all the terms and conditions before placing the order.')));
                    return $information;
                }
            }
            if ($check_card) {
                $dataPayment = $check_card;
                if (version_compare(Mage::getVersion(), '1.8.0.0', '>=') === true) {
                    $dataPayment['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
                            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                            | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                            | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                }
            }

            $this->_getOnepage()->getQuote()->getPayment()->importData($dataPayment);

            $paymentRedirect = $this->_getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if($paymentRedirect && $paymentRedirect != '') {
                $data_return = array(
                    'payment_redirect' => 1,
                    'payment_method' => 'paypal_express',
                );
                $information['data'] = array($data_return);
                return $information;
            }
            $this->_getOnepage()->saveOrder();
			$redirectUrl = $this->_getOnepage()->getCheckout()->getRedirectUrl();
            // } catch (Mage_Payment_Model_Info_Exception $e) {
            //} catch (Mage_Core_Exception $e) {                       
        } catch (Exception $e) {
            $information = '';
            if (is_array($e->getMessage())) {
                $information = $this->statusError($e->getMessage());
            } else {
                $information = $this->statusError(array($e->getMessage()));
            }
            $this->_getOnepage()->getCheckout()->setUpdateSection(null);
//            $this->cleanSession();
            return $information;
        }
        $this->_getOnepage()->getQuote()->save();
        $information = $this->statusSuccess();
		$data_return = array(
			'invoice_number' => $this->_getCheckoutSession()->getLastRealOrderId(),
			'payment_method' => $this->_getOnepage()->getQuote()->getPayment()->getMethodInstance()->getCode(),
		);		
		//update hai.ta 19/11/2014
		$event_name = 'simicart_after_place_order';
		$event_value = array(
				'object' => $this,								
		);
		$data_return = $this->changeData($data_return, $event_name, $event_value);
		//end update
        $information['data'] = array($data_return);
        $message_success = Mage::helper('checkout')->__("Thank you for your purchase!");
        $information['message'] = array($message_success);
        Mage::getSingleton('core/session')->unsSimiAddress();
        Mage::getSingleton('core/session')->unsSimiShippingMethod();
        $this->cleanSession();
        return $information;
    }
}
