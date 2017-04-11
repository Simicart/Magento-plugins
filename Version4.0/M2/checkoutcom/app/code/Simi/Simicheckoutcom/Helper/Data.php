<?php

/**
 * Connector data helper
 */

namespace Simi\Simicheckoutcom\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Simi\Simiconnector\Helper\Data
{
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    public function getPaymentToken($orderId) {
        $order = $this->simiObjectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($orderId);
        $paramArray = array();
        $paramArray['autoCapTime'] = '24';
        $paramArray['autoCapture'] = 'Y';
        $paramArray['chargeMode'] = 1;
        $paramArray['email'] = $order->getData('customer_email');
        $paramArray['customerName'] = $order->getData('customer_firstname') . ' ' . $order->getData('customer_middlename') . ' ' . $order->getData('customer_lastname');
        $paramArray['description'] = __('Checkout.com Order');
        $paramArray['value'] = floatval($order->getData('grand_total')) * 100;
        $paramArray['currency'] = $order->getData('order_currency_code');
        $paramArray['trackId'] = $orderId;
        $paramArray['transactionIndicator'] = '1';
        $paramArray['customerIp'] = $_SERVER['REMOTE_ADDR'];
        $paramArray['cardToken'] = '';

        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress)
            $shippingAddress = $order->getBillingAddress();

        $paramArray['shippingDetails'] = array(
            'addressLine1' => $shippingAddress->getData('street'),
            'addressLine2' => $shippingAddress->getData('city'),
            'postcode' => $shippingAddress->getData('postcode'),
            'country' => $shippingAddress->getData('country_id'),
            'city' => $shippingAddress->getData('city'),
            'state' => $shippingAddress->getData('region'),
            'phone' => array('countryCode' => '',
                'number' => $shippingAddress->getData('telephone')),
        );

        $productInfo = array();
        $itemCollection = $order->getAllVisibleItems();
        foreach ($itemCollection as $item) {
            $productInfo[] = array(
                'description' => $item->getData('name'),
                'image' => NULL,
                'name' => $item->getData('name'),
                'price' => $item->getData('price_incl_tax'),
                'quantity' => (int) $item->getData('qty_ordered'),
                'shippingCost' => 0,
                'sku' => $item->getData('sku'),
                'trackingUrl' => '',
            );
        }
        //$paramArray['products'] = $productInfo;
        if ($this->getStoreConfig("payment/simicheckoutcom/is_sandbox"))
            $url = 'https://sandbox.checkout.com/api2/v2/tokens/payment';
        else 
            $url = 'https://api2.checkout.com/v2/tokens/payment';
        $currentUrl = $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                ->getUrl('*/*/Paymentrestv22').'?order_id='.$orderId;
        echo ' 
            <script>
            var http = new XMLHttpRequest();
            var url = "'.$url.'";
            var params = \''.  json_encode($paramArray).'\';
            http.open("POST", url, true);
            //Send the proper header information along with the request
            http.setRequestHeader("Content-type", "application/json;charset=UTF-8");
            http.setRequestHeader("Authorization", "'.$this->getStoreConfig("payment/simicheckoutcom/private_key").'");

            http.onreadystatechange = function() {//Call a function when the state changes.
                if(http.readyState == 4 && http.status == 200) {
                    var returned = JSON.parse(http.responseText);
                    window.location = "'.$currentUrl.'"+"&payment_token="+returned.id;
                }
            }
            http.send(params);
            </script>
        ';
    }
}
