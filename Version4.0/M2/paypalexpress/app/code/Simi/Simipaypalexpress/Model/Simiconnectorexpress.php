<?php

namespace Simi\Simipaypalexpress\Model;

/**
 * Connector Model
 *
 * @method \Simi\Simiconnector\Model\Resource\Page _getResource()
 * @method \Simi\Simiconnector\Model\Resource\Page getResource()
 */
class Simiconnectorexpress extends \Magento\Framework\Model\AbstractModel
{

    public function getAddress($data) {
        return Mage::helper('simiconnector/address')->getAddressDetail($data);
    }

    public function updateAddress($parameters) {
        $checkout = $this->_getOnepage();
        $this->_quote->setTotalsCollectedFlag(true);
        $checkout->setQuote($this->_quote);
        if (isset($parameters['s_address'])) {
            Mage::helper('simiconnector/address')->saveShippingAddress($parameters['s_address']);
        }
        if (isset($parameters['b_address'])) {
            Mage::helper('simiconnector/address')->saveBillingAddress($parameters['b_address']);
        }
        $this->_quote->setTotalsCollectedFlag(false);
        $this->_quote->collectTotals();
        $this->_quote->setDataChanges(true);
        $this->_quote->save();
    }

    /*
     * Get Billing and Shipping address
     */

    public function getBillingShippingAddress() {
        $info = array();
        $shippingAddress = $this->getAddress($this->getShippingAddress());
        $billingAddress = $this->getAddress($this->getBillingAddress());
        $billingAddress["address_id"] = $this->getBillingAddress()->getId();
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $billingAddress['email'] = $this->getBillingAddress()->getEmail();
            $shippingAddres['email'] = $this->getBillingAddress()->getEmail();
            $info[] = array(
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
            );
        } else {
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
}
