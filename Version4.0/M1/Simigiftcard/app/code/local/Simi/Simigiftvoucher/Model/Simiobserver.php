<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/4/17
 * Time: 10:19 AM
 */
class Simi_Simigiftvoucher_Model_Simiobserver {

    protected function _getSession() {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getQuote() {
        return $this->_getCart()->getQuote();
    }



    public function simiSimiconnectorModelServerInitialize($observer){
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'simigiftcode'){
            $observerObjectData['module'] = 'simigiftvoucher';
        } elseif ($observerObjectData['resource'] == 'simicustomercredit'){
            $observerObjectData['module'] = 'simigiftvoucher';
        }
        elseif ($observerObjectData['resource'] == 'simigiftcard'){
            $observerObjectData['module'] = 'simigiftvoucher';
        }
        elseif ($observerObjectData['resource'] == 'checkgiftcard'){
            $observerObjectData['module'] = 'simigiftvoucher';
        }
        elseif ($observerObjectData['resource'] == 'giftvouchercheckout'){
            $observerObjectData['module'] = 'simigiftvoucher';
        }
        $observerObject->setData($observerObjectData);
    }

    public function simiSimiconnectorModelApiQuoteitemsIndexAfter($observer){

            $quoteItemApi = $observer->getObject();
            $detail_list = $quoteItemApi->detail_list;
            $count = 0;
            $items = $this->_getQuote()->getAllItems();
            foreach ($items as $item){
                $data = $item->getData();
                if ($data['product_type'] == 'simigiftvoucher') {
                    $count++;
                }
            }
            if ($count == count($items)){
                $detail_list['gift_card']['use_giftcard'] = false;
                $detail_list['gift_card']['label'] = Mage::helper('simigiftvoucher')->__('Gift Cards cannot be used to purchase Gift Card products');
            }else {
                $detail_list['gift_card']['use_giftcard'] = true;
            }
            if ($detail_list['gift_card']['use_giftcard']){
                if (Mage::getSingleton('customer/session')->isLoggedIn()){
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    if (Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit')){
                        $credit = Mage::getModel('simigiftvoucher/credit')->load($customer->getId(),'customer_id');
                        if ($credit->getBalance() > 0.0001){
                            $detail_list['gift_card']['customer'] = $credit->getData();
                            $detail_list['gift_card']['customer']['balance'] = $this->formatBalance($credit, true);
                            if($this->getUseGiftCredit()){
                                $detail_list['gift_card']['credit']['use_credit'] = 1;
                                $detail_list['gift_card']['credit']['use_credit_amount'] = $this->getUseGiftCreditAmount();
                            }
                            else{
                                $detail_list['gift_card']['credit']['use_credit'] = 0;
                            }
                        }

                    }
                    if ($this->getUseGiftVoucher()){
                        $detail_list['gift_card']['giftcode']['use_giftcode'] = 1;
                    }
                    else {
                        $detail_list['gift_card']['giftcode']['use_giftcode'] = 0;
                    }
                    $list_code = Mage::getModel('simigiftvoucher/simimapping')->getExistedGiftCard();
                    $detail_list['gift_card']['customer']['list_code'] = $list_code;
                    $discounts = $this->getGiftVoucherDiscount();
                    if (count($discounts)){
                        foreach ($discounts as $code => $discount){
                            if($discount <=0){
                                $detail_list['gift_card']['giftcode']['messages'] = Mage::helper('simigiftvoucher')->__('Gift code "%s" hasn\'t been used yet.', Mage::helper('simigiftvoucher')->getHiddenCode($code));
                            }
                        }

                        foreach ($discounts as $code => $discount){
                            if($discount <= 0){
                                continue;
                            }
                            $detail_list['gift_card']['giftcode'][] = array(
                                "giftcode"  => $code,
                                "giftcode_hidden"  =>  Mage::helper('simigiftvoucher')->getHiddenCode($code),
                                "amount"    =>  $discount
                            );
                        }
                    }

                }
            }
            $quoteitems_detail = array();
            $quoteitems = $detail_list['quoteitems'];
            foreach ($quoteitems as $item){
                $item['giftcard_option'] = $this->getGiftcardOptions($item['item_id']);
                $quoteitems_detail[] = $item;
            }
            $detail_list['quoteitems'] =  $quoteitems_detail;
            $quoteItemApi->detail_list = $detail_list;


    }

    public function simiSimiconnectorModelApiOrdersOnepageShowAfter($observer){
        $onepageApi = $observer->getObject();
        $detail_onepage = $onepageApi->detail_onepage;
        $count = 0;
        $items = $this->_getQuote()->getAllItems();
        foreach ($items as $item){
            $data = $item->getData();
            if ($data['product_type'] == 'simigiftvoucher') {
                $count++;
            }
        }

        if ($count == count($items)){
            $detail_onepage['gift_card']['use_giftcard'] = false;
            $detail_onepage['gift_card']['label'] = Mage::helper('simigiftvoucher')->__('Gift Cards cannot be used to purchase Gift Card products');
        }else {
            $detail_onepage['gift_card']['use_giftcard'] = true;
        }

        if ($detail_onepage['gift_card']['use_giftcard']){
            if (Mage::getSingleton('customer/session')->isLoggedIn()){
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                if (Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit')){
                    $credit = Mage::getModel('simigiftvoucher/credit')->load($customer->getId(),'customer_id');
                    if ($credit->getBalance() > 0.0001){
                        $detail_onepage['gift_card']['customer'] = $credit->getData();
                        $detail_onepage['gift_card']['customer']['balance'] = $this->formatBalance($credit, true);
                        if($this->getUseGiftCredit()){
                            $detail_onepage['gift_card']['credit']['use_credit'] = 1;
                            $detail_onepage['gift_card']['credit']['use_credit_amount'] = $this->getUseGiftCreditAmount();
                        }
                        else{
                            $detail_onepage['gift_card']['credit']['use_credit'] = 0;
                        }
                    }

                }
                if ($this->getUseGiftVoucher()){
                    $detail_onepage['gift_card']['giftcode']['use_giftcode'] = 1;
                }
                else {
                    $detail_onepage['gift_card']['giftcode']['use_giftcode'] = 0;
                }
                $list_code = Mage::getModel('simigiftvoucher/simimapping')->getExistedGiftCard();
                $detail_onepage['gift_card']['customer']['list_code'] = $list_code;
                $discounts = $this->getGiftVoucherDiscount();
                if (count($discounts)){
                    foreach ($discounts as $code => $discount){
                        if($discount <=0){
                            $detail_onepage['gift_card']['giftcode']['messages'] = Mage::helper('simigiftvoucher')->__('Gift code "%s" hasn\'t been used yet.', Mage::helper('simigiftvoucher')->getHiddenCode($code));
                        }
                    }

                    foreach ($discounts as $code => $discount){
                        if($discount <= 0){
                            continue;
                        }
                        $detail_onepage['gift_card']['giftcode'][] = array(
                            "giftcode"  => $code,
                            "giftcode_hidden"  =>  Mage::helper('simigiftvoucher')->getHiddenCode($code),
                            "amount"    =>  $discount
                        );
                    }
                }

            }
        }
        $onepageApi->detail_onepage = $detail_onepage;
    }

    /**
     * Check customer use gift credit to checkout
     *
     * @return boolean
     */
    public function getUseGiftCredit()
    {
        return Mage::getSingleton('checkout/session')->getUseGiftCardCredit();
    }

    /**
     * @return string
     */
    public function getUsingAmount()
    {
        return Mage::app()->getStore()->formatPrice(
            Mage::getSingleton('checkout/session')->getUseGiftCreditAmount()
        );
    }

    /**
     * @return mixed
     */
    public function getUseGiftCreditAmount()
    {
        return Mage::getSingleton('checkout/session')->getMaxCreditUsed();
    }

    /**
     * @return array
     */
    public function getGiftVoucherDiscount()
    {
        $session = Mage::getSingleton('checkout/session');
        $discounts = array();
        if ($codes = $session->getGiftCodes()) {
            $codesArray = explode(',', $codes);
            $codesDiscountArray = explode(',', $session->getCodesDiscount());
            $discounts = array_combine($codesArray, $codesDiscountArray);
        }

        return $discounts;
    }

    /**
     * check customer use gift card to checkout
     *
     * @return boolean
     */
    public function getUseGiftVoucher()
    {
        return Mage::getSingleton('checkout/session')->getUseGiftCard();
    }

    /**
     * Returns the formatted Gift Card balance
     *
     * @param mixed $credit
     * @param boolean $showUpdate
     * @return string
     */
    public function formatBalance($credit, $showUpdate = false)
    {
        if ($showUpdate) {
            $cardCurrency = Mage::getModel('directory/currency')->load($credit->getCurrency());
            /* @var Mage_Core_Model_Store */
            $store = Mage::app()->getStore();
            $baseCurrency = $store->getBaseCurrency();
            $currentCurrency = $store->getCurrentCurrency();
            if ($cardCurrency->getCode() == $currentCurrency->getCode()) {
                return $store->formatPrice($credit->getBalance() - $this->getUseGiftCreditAmount());
            }
            if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
                $amount = $store->convertPrice($credit->getBalance(), false);
                return $store->formatPrice($amount - $this->getUseGiftCreditAmount());
            }
            if ($baseCurrency->convert(100, $cardCurrency)) {
                $amount = $credit->getBalance() * $baseCurrency->convert(100, $currentCurrency)
                    / $baseCurrency->convert(100, $cardCurrency);
                return $store->formatPrice($amount - $this->getUseGiftCreditAmount());
            }
            return $cardCurrency->format($credit->getBalance(), array(), true);
        }
        return $this->getGiftCardBalance($credit);
    }

    /**
     * Get the balance of Gift Card
     *
     * @param mixed $item
     * @return string
     */
    public function getGiftCardBalance($item)
    {
        $cardCurrency = Mage::getModel('directory/currency')->load($item->getCurrency());
        /* @var Mage_Core_Model_Store */
        $store = Mage::app()->getStore();
        $baseCurrency = $store->getBaseCurrency();
        $currentCurrency = $store->getCurrentCurrency();
        if ($cardCurrency->getCode() == $currentCurrency->getCode()) {
            return $store->formatPrice($item->getBalance());
        }
        if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
            return $store->convertPrice($item->getBalance(), true);
        }
        if ($baseCurrency->convert(100, $cardCurrency)) {
            $amount = $item->getBalance() * $baseCurrency->convert(100, $currentCurrency)
                / $baseCurrency->convert(100, $cardCurrency);
            return $store->formatPrice($amount);
        }
        return $cardCurrency->format($item->getBalance(), array(), true);
    }

    /**
     * @return array
     */
    public function getGiftcardOptions($optionId)
    {

        $options = Mage::getModel('sales/quote_item_option')
            ->getCollection()->addItemFilter($optionId);
        $formData = array();
        //zend_debug::dump($options->getData());die('xx');
        $result = array();
        foreach ($options as $option) {
            $result[$option->getCode()] = $option->getValue();
        }

        if (isset($result['base_gc_value'])) {
            if (isset($result['gc_product_type']) && $result['gc_product_type'] == 'range') {
                $currency = $store->getCurrentCurrencyCode();
                $baseCurrencyCode = $store->getBaseCurrencyCode();

                if ($currency != $baseCurrencyCode) {
                    $currentCurrency = Mage::getModel('directory/currency')->load($currency);
                    $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);

                    $value = $baseCurrency->convert($result['base_gc_value'], $currentCurrency);
                } else {
                    $value = $result['base_gc_value'];
                }
            }
        }

        foreach ($options as $option) {
            if ($option->getCode() == 'amount') {
                if (isset($value)) {
                    $formData[$option->getCode()] = $value;
                } else {
                    $formData[$option->getCode()] = $option->getValue();
                }
            } else {
                $formData[$option->getCode()] = $option->getValue();
            }
        }
        return $formData;
    }
}