<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_PayPalExpress
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * PayPalExpress Model
 * 
 * @category 	Magestore
 * @package 	Magestore_PayPalExpress
 * @author  	Magestore Developer
 */
class Simi_PayPalExpress_Model_Express extends Simi_Connector_Model_Abstract
{
	/**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Currently selected shipping rate
     *
     * @var Mage_Sales_Model_Quote_Addressrate
     */
    protected $_currentShippingRate = null;

    /**
     * Paypal action prefix
     *
     * @var string
     */
    protected $_paypalActionPrefix = 'paypal';
	
	public function _getOnepage() {
        return Mage::getModel('checkout/type_onepage');
    }
    /**
     * Quote object setter
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Paypal_Block_Express_Review
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }
	/**
     * Return quote billing address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->_quote->getBillingAddress();
    }

    /**
     * Return quote shipping address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        if ($this->_quote->getIsVirtual()) {
            return array();
        }
        return $this->_quote->getShippingAddress();
    }
    
    /**
     * Return carrier name from config, base on carrier code
     *
     * @param $carrierCode string
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig("carriers/{$carrierCode}/title")) {
            return $name;
        }
        return $carrierCode;
    }

    /**
     * Get either shipping rate code or empty value on error
     *
     * @param Varien_Object $rate
     * @return string
     */
    public function renderShippingRateValue(Varien_Object $rate)
    {
        if ($rate->getErrorMessage()) {
            return '';
        }
        return $rate->getCode();
    }

    /**
     * Get shipping rate code title and its price or error message
     *
     * @param Varien_Object $rate
     * @param string $format
     * @param string $inclTaxFormat
     * @return string
     */
    public function renderShippingRateOption($rate, $format = '%s - %s%s', $inclTaxFormat = ' (%s %s)')
    {
        $renderedInclTax = '';
        if ($rate->getErrorMessage()) {
            $price = $rate->getErrorMessage();
        } else {
            $price = $this->_getShippingPrice($rate->getPrice(),
                $this->helper('tax')->displayShippingPriceIncludingTax());

            $incl = $this->_getShippingPrice($rate->getPrice(), true);
            if (($incl != $price) && $this->helper('tax')->displayShippingBothPrices()) {
                $renderedInclTax = sprintf($inclTaxFormat, Mage::helper('tax')->__('Incl. Tax'), $incl);
            }
        }
        return sprintf($format, $this->escapeHtml($rate->getMethodTitle()), $price, $renderedInclTax);
    }

    /**
     * Getter for current shipping rate
     *
     * @return Mage_Sales_Model_Quote_Addressrate
     */
    public function getCurrentShippingRate()
    {
        return $this->_currentShippingRate;
    }

    /**
     * Set paypal actions prefix
     */
    public function setPaypalActionPrefix($prefix)
    {
        $this->_paypalActionPrefix = $prefix;
    }

    /**
     * Return formatted shipping price
     *
     * @param float $price
     * @param bool $isInclTax
     *
     * @return bool
     */
    protected function _getShippingPrice($price, $isInclTax)
    {
        return $this->_formatPrice($this->helper('tax')->getShippingPrice($price, $isInclTax, $this->_address));
    }

    /**
     * Format price base on store convert price method
     *
     * @param float $price
     * @return string
     */
    protected function _formatPrice($price)
    {
        return $this->_quote->getStore()->convertPrice($price, true);
    }
	
	public function getBillingShippingAddress(){
		$info = array();		
	
		//$billingAddress = $this->getAddress($this->getBillingAddress());
		$shippingAddress = $this->getAddress($this->getShippingAddress());
		$billingAddress = $shippingAddress;
		$billingAddress["address_id"] = $this->getBillingAddress()->getId();
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$billingAddress['email'] = $this->getBillingAddress()->getEmail();
			$shippingAddres['email'] = $this->getBillingAddress()->getEmail();
			$info[] = array(
				'billing_address' => $billingAddress,
				'shipping_address' => $shippingAddress,				
			);
		}else{
			$billingAddress['email'] = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			$shippingAddres['email'] = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			$info[] = array(
				'billing_address' => $billingAddress,
				'shipping_address' => $shippingAddress,				
			);
		}		
		
		$data = $this->statusSuccess();
		$data['data'] = $info;
		return $data;
	}
	
	public function updateAddress($data){					
		$billing_address = Mage::helper('connector/checkout')->convertBillingAddress($data);
		if(isset($data->shippingAddress)){
			$shipping_address = Mage::helper('connector/checkout')->convertShippingAddress($data); 
		}				
		// Zend_debug::dump($shipping_address);
		// die();
		$result = array();
        try {
			$checkout = $this->_getOnepage();
			$this->_quote->setTotalsCollectedFlag(true);
			$checkout->setQuote($this->_quote);
			// Zend_debug::dump($billing_address);
            $result_billing = $checkout->saveBilling($billing_address, 0);
			// Zend_debug::dump($result_billing);
			if(isset($data->shippingAddress)){
				$result_shipping = $checkout->saveShipping($shipping_address, 0);			 
				// Zend_debug::dump($result_shipping);
				if(isset($result_billing['message']) && isset($result_shipping['message']) ){
					$result = array_merge($result_billing['message'], $result_shipping['message']);
					$result = array_unique($result);
				}  
			}else{
				if(isset($result_billing['message'])){
					$result = $result_billing['message'];					
				}  
			}                        			         
			
			$this->_quote->setTotalsCollectedFlag(false);
			$this->_quote->collectTotals();
			$this->_quote->setDataChanges(true);
			$this->_quote->save();
        } catch (Exception $e) {
            if (is_array($e->getMessage())) {
                $information = $this->statusError($e->getMessage());
            } else {
                $information = $this->statusError(array($e->getMessage()));
            }
            return $information;
        }

        if (count($result)) {
              $result = array_unique($result);
              $information = $this->statusError($result);
              return $information;
        }				
		
		$shippingAddress = $this->getShippingAddress();
		$data = $this->statusSuccess();
		$data['data'] = $this->getShippingMethods($shippingAddress);
		return $data;
	}
	
	public function getAddress($data){
		$street = $data->getStreet();
		if($street == NUll){
			$street = array();
			$street[0] = "";
		}
		return array(
			'address_id' => $data->getId(),
            'name' => $data->getFirstname() . " " . $data->getLastname(),
            'prefix' => $data->getPrefix() != NULL ? $data->getPrefix() : "",
            'suffix' => $data->getSuffix() != NULL ? $data->getSuffix() : "",
            'taxvat' => $data->getVatId() != NULL ? $data->getVatId() : "",
			'vat_id' => $data->getVatId() != NULL ? $data->getVatId() : "",
            'street' => $street[0],
            'city' => $data->getCity() != NULL ? $data->getCity() : "",
            'state_name' => $data->getRegion() != NULL ? $data->getRegion() : "",
            'state_id' => $data->getRegionId() != NULL ? $data->getRegionId() : "",
            'state_code' => $data->getRegionCode() != NULL ? $data->getRegionCode() : "",
            'zip' => $data->getPostcode() != NULL ? $data->getPostcode() : "",
            'country_name' => $data->getCountryModel()->loadByCode($data->getCountry())->getName() != NULL ? $data->getCountryModel()->loadByCode($data->getCountry())->getName() : "",
            'country_code' => $data->getCountry() != NULL ? $data->getCountry() : "",
            'phone' => $data->getTelephone() != NULL ? $data->getTelephone() : "N/A",
		);		
	}
	
	public function getShippingMethods($shipping){
		$info = array();
		$groups = $shipping->getGroupedAllShippingRates();
		if ($groups && $shipping) {					
			// if($this->_quote->getMayEditShippingAddress() || $this->_quote->getMayEditShippingMethod()){			 
				// die("x");
				foreach ($groups as $code => $rates){
					foreach ($rates as $rate){
						if($rate->getData('error_message') != NULL){
							continue;
						}						
						$select = false;
						// if($shipping->getShippingMethod() != null && $shipping->getShippingMethod() == $_rate->getCode()){
							// $select = true;
						// }				
						$s_fee = Mage::getModel('connector/checkout_shipping')->getShippingPrice($rate->getPrice(), Mage::helper('tax')->displayShippingPriceIncludingTax());
						$s_fee_incl = Mage::getModel('connector/checkout_shipping')->getShippingPrice($rate->getPrice(), true);
						if (Mage::helper('tax')->displayShippingBothPrices() && $s_fee != $s_fee_incl){
							$info[] = array(
								's_method_id' => $rate->getId(),
								's_method_code' => $rate->getCode(),
								's_method_title' => $rate->getCarrierTitle(),
								's_method_fee' => Mage::app()->getStore()->convertPrice(floatval($s_fee), false),
								's_method_fee_incl_tax' => $s_fee_incl,
								's_method_name' => $rate->getMethodTitle(),
								's_method_selected' => $select,
							);
						}else{
							 $info[] = array(
								's_method_id' => $rate->getId(),
								's_method_code' => $rate->getCode(),
								's_method_title' => $rate->getCarrierTitle(),
								's_method_fee' => $s_fee,			
								's_method_name' => $rate->getMethodTitle(),
								's_method_selected' => $select,
							);
						}
					}					
				}
			// }
		}	
		return $info;
	}
	
	public function getTotal(){
		$total = $this->_quote->getTotals();
		$fee_v2 = array();
        Mage::helper('connector/checkout')->setTotal($total, $fee_v2);
		return $fee_v2;
	}	
	
	public function getInfomation(){				
		$info = $this->getShippingMethods($this->getShippingAddress());				
		$data = $this->statusSuccess();
		$data['data'] = $info;		
		return $data;
	}
}