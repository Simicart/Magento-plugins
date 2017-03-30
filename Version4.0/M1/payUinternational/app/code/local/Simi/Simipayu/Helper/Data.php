<?php

class Simi_Simipayu_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getFormFields($order_id) {
        $merchant_id = Mage::getStoreConfig('payment/simipayu/merchant_id');
        $secure_key = Mage::getStoreConfig('payment/simipayu/secure_key');
        $account_id = Mage::getStoreConfig('payment/simipayu/account_id');
        $gateway_url = Mage::getStoreConfig('payment/simipayu/gateway_url');
        $transaction_mode = Mage::getStoreConfig('payment/simipayu/transaction_mode');
        $orderIncrementId = $order_id;

        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        $currency = $order->getOrderCurrencyCode();

        $BAddress = $order->getBillingAddress();

        //$paymentAmount = $order->getBaseGrandTotal();
        $paymentAmount = number_format($order->getGrandTotal(), 2, '.', '');
        $tax = number_format($order->getTaxAmount(), 2, '.', '');

        $taxReturnBase = number_format(($paymentAmount - $tax), 2, '.', '');
        if ($tax == 0)
            $taxReturnBase = 0;
        //$taxReturnBase = $tax = 0;

        $ProductName = '';
        $items = $order->getAllItems();
        if ($items) {
            foreach ($items as $item) {
                if ($item->getParentItem())
                    continue;
                $ProductName .= $item->getName() . '; ';
            }
        }
        $ProductName = rtrim($ProductName, '; ');

        $signature = md5($secure_key . '~' . $merchant_id . '~' . $orderIncrementId . '~' . $paymentAmount . '~' . $currency);

        $test = 0;
        if ($transaction_mode == 'test')
            $test = 1;

        $params = array(
            'merchantId' => $merchant_id,
            'referenceCode' => $orderIncrementId,
            'description' => $ProductName,
            'amount' => $paymentAmount,
            'tax' => $tax,
            'taxReturnBase' => $taxReturnBase,
            'signature' => $signature,
            'accountId' => $account_id,
            'currency' => $currency,
            'buyerEmail' => $order->getCustomerEmail(),
            'test' => $test,
            'confirmationUrl' => Mage::getUrl('simipayu/api/notify'),
            'responseUrl' => Mage::getUrl('simipayu/api/success'),
                //'gateway_url'		=>	$gateway_url,
        );

        return $this->getRequestUrl($params);
    }

    /**
     * Return true if the method can be used at this time
     *
     * @return bool
     */
    public function isAvailable($quote = null) {
        return true;
    }

    public function getRequestUrl($data) {
        $gateway_url = $this->getGatewayUrl();

        $merchant_data = "?";
        foreach ($data as $key => $value) {
            $merchant_data.=$key . '=' . $value . '&';
        }


        return $gateway_url .= $merchant_data;
    }

    public function getGatewayUrl() {
        return Mage::getStoreConfig('payment/simipayu/gateway_url');
    }

}
