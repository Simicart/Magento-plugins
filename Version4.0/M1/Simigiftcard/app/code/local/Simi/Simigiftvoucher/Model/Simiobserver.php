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
        if ($observerObjectData['resource'] == 'simigiftcodes'){
            $observerObjectData['module'] = 'simigiftvoucher';
        } elseif ($observerObjectData['resource'] == 'simicustomercredits'){
            $observerObjectData['module'] = 'simigiftvoucher';
        }
        elseif ($observerObjectData['resource'] == 'simigiftcards'){
            $observerObjectData['module'] = 'simigiftvoucher';
        }
        elseif ($observerObjectData['resource'] == 'checkgiftcodes'){
            $observerObjectData['module'] = 'simigiftvoucher';
        }
        elseif ($observerObjectData['resource'] == 'giftvouchercheckouts'){
            $observerObjectData['module'] = 'simigiftvoucher';
        }
        elseif ($observerObjectData['resource'] == 'simitimezones'){
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
                            $detail_list['gift_card']['customer']['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                            $detail_list['gift_card']['customer']['currency_symbol'] = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                            if($this->getUseGiftCredit()){
                                $detail_list['gift_card']['credit']['use_credit'] = 1;
                                $detail_list['gift_card']['credit']['use_credit_amount'] = $this->getSimiuseGiftCreditAmount();
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
                            $detail_list['gift_card']['giftcode'][] = array(
                                "gift_code"  => $code,
                                "hidden_code"  =>  Mage::helper('simigiftvoucher')->getHiddenCode($code),
                                "amount"    =>  $discount
                            );
                        }
                    }

                }
            }
            $quoteitems_detail = array();
            $quoteitems = $detail_list['quoteitems'];
            foreach ($quoteitems as $item){
                if ($item['product_type'] == 'simigiftvoucher'){
                    $item['option'] = $this->getGiftcardOptions($item['item_id']);
                    $item['image'] = $this->getGiftcardOptions($item['item_id'], true);
                }
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
            $detail_onepage['order']['gift_card']['use_giftcard'] = false;
            $detail_onepage['order']['gift_card']['label'] = Mage::helper('simigiftvoucher')->__('Gift Cards cannot be used to purchase Gift Card products');
        }else {
            $detail_onepage['order']['gift_card']['use_giftcard'] = true;
        }

        if ($detail_onepage['order']['gift_card']['use_giftcard']){
            if (Mage::getSingleton('customer/session')->isLoggedIn()){
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                if (Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit')){
                    $credit = Mage::getModel('simigiftvoucher/credit')->load($customer->getId(),'customer_id');
                    if ($credit->getBalance() > 0.0001){
                        $detail_onepage['order']['gift_card']['customer'] = $credit->getData();
                        $detail_onepage['order']['gift_card']['customer']['balance'] = $this->formatBalance($credit, true);
                        $detail_onepage['order']['gift_card']['customer']['currency_symbol'] = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                        if($this->getUseGiftCredit()){
                            $detail_onepage['order']['gift_card']['credit']['use_credit'] = 1;
                            $detail_onepage['order']['gift_card']['credit']['use_credit_amount'] = $this->getSimiuseGiftCreditAmount();
                        }
                        else{
                            $detail_onepage['order']['gift_card']['credit']['use_credit'] = 0;
                        }
                    }

                }
                if ($this->getUseGiftVoucher()){
                    $detail_onepage['order']['gift_card']['giftcode']['use_giftcode'] = 1;
                }
                else {
                    $detail_onepage['order']['gift_card']['giftcode']['use_giftcode'] = 0;
                }
                $list_code = Mage::getModel('simigiftvoucher/simimapping')->getExistedGiftCard();
                $detail_onepage['order']['gift_card']['customer']['list_code'] = $list_code;
                $discounts = $this->getGiftVoucherDiscount();
                if (count($discounts)){
                    foreach ($discounts as $code => $discount){
                        /*if($discount <=0){
                            $error = Mage::helper('simigiftvoucher')->__('Gift code "%s" hasn\'t been used yet.', Mage::helper('simigiftvoucher')->getHiddenCode($code));
                            $detail_list['order']['gift_card']['notice'] = $error;
                        }*/
                        $detail_onepage['order']['gift_card']['giftcode'][] = array(
                            "gift_code"  => $code,
                            "hidden_code"  =>  Mage::helper('simigiftvoucher')->getHiddenCode($code),
                            "amount"    =>  $discount
                        );
                    }
                }

            }
        }
        $onepageApi->detail_onepage = $detail_onepage;
    }

    public function simiSimiconnectorHelperTotalSetTotalAfter($observer){
        $orderTotalHelper = $observer->getObject();
        $giftcodes = null;
        $creditDiscount = 0;
        //zend_debug::dump($this->_getSession()->getData());
        foreach ($this->_getQuote()->getAllAddresses() as $address) {
            //zend_debug::dump($address->debug());
            $giftVoucherDiscount = $address->getSimigiftVoucherDiscount();
            $amount = $address->getSimiuseGiftCreditAmount();
            if ($giftVoucherDiscount > 0){
                $giftcodes = $address->getSimigiftCodes();
                $codediscount = $address->getSimicodesDiscount();
            }
            if ($amount > 0) {
                $creditDiscount = (int) $amount;
            }
        }//die;
        $giftcodes = explode(',',$giftcodes);
        $codediscount = explode(',',$codediscount);
        $position = 5;
        if ($giftcodes){
            foreach ($giftcodes as $key => $code){
                if ($code){
                    $orderTotalHelper->addCustomRow(Mage::helper('simigiftvoucher')->__($code),$position,$codediscount[$key]);
                    $position += 1;
                }
            }
        }
        if ($creditDiscount){
            $orderTotalHelper->addCustomRow(Mage::helper('simigiftvoucher')->__('Giftvoucher Credit'),$position,$creditDiscount);
        }

    }

    /**
     * Check customer use gift credit to checkout
     *
     * @return boolean
     */
    public function getUseGiftCredit()
    {
        return Mage::getSingleton('checkout/session')->getSimiuseGiftCardCredit();
    }

    /**
     * @return string
     */
    public function getUsingAmount()
    {
        return Mage::app()->getStore()->formatPrice(
            Mage::getSingleton('checkout/session')->getSimiuseGiftCreditAmount()
        );
    }

    /**
     * @return mixed
     */
    public function getSimiuseGiftCreditAmount()
    {
        return Mage::getSingleton('checkout/session')->getSimiuseGiftCreditAmount();
    }

    /**
     * @return array
     */
    public function getGiftVoucherDiscount()
    {
        $session = $this->_getSession();
        $discounts = array();
        if ($codes = $session->getSimigiftCodes()) {
            $codesArray = explode(',', $codes);
            $codesDiscountArray = explode(',', $session->getSimicodesDiscount());
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
        return Mage::getSingleton('checkout/session')->getSimiuseGiftCard();
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
                return $store->formatPrice($credit->getBalance() - $this->getSimiuseGiftCreditAmount(),false);
            }
            if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
                $amount = $store->convertPrice($credit->getBalance(), false);
                return $store->formatPrice($amount - $this->getSimiuseGiftCreditAmount(),false);
            }
            if ($baseCurrency->convert(100, $cardCurrency)) {
                $amount = $credit->getBalance() * $baseCurrency->convert(100, $currentCurrency)
                    / $baseCurrency->convert(100, $cardCurrency);
                return $store->formatPrice($amount - $this->getSimiuseGiftCreditAmount(),false);
            }
            return $cardCurrency->format($credit->getBalance(), array(), false);
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
            return $store->formatPrice($item->getBalance(),false);
        }
        if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
            return $store->convertPrice($item->getBalance(), true);
        }
        if ($baseCurrency->convert(100, $cardCurrency)) {
            $amount = $item->getBalance() * $baseCurrency->convert(100, $currentCurrency)
                / $baseCurrency->convert(100, $cardCurrency);
            return $store->formatPrice($amount,false);
        }
        return $cardCurrency->format($item->getBalance(), array(), false);
    }

    /**
     * @return array
     */
    public function getGiftcardOptions($optionId,$getUrlImage = false)
    {
        $store = Mage::app()->getStore();
        $options = Mage::getModel('sales/quote_item_option')
            ->getCollection()->addItemFilter($optionId);
        $formData = array();

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
        $info_buyRequest = unserialize($formData['info_buyRequest']);
        if ($getUrlImage && isset($info_buyRequest['url_image'])){
            return $info_buyRequest['url_image'];
        }
        $opt = array();
        foreach (Mage::helper('simigiftvoucher')->getGiftVoucherOptions() as $code => $label) {
            if ($formData[$code]){
                if ($code == 'giftcard_template_id') {
                    $valueTemplate = Mage::getModel('simigiftvoucher/gifttemplate')->load($formData[$code]);
                    $opt[] = array(
                        'option_title' => $label,
                        'option_value' => Mage::helper('core')->htmlEscape($valueTemplate->getTemplateName() ?
                            $valueTemplate->getTemplateName() : $formData[$code]),
                    );
                }else if ($code == 'amount') {
                    $opt[] = array(
                        'option_title' => $label,
                        'option_value' => Mage::helper('core')->formatPrice($formData[$code],false),
                    );
                } elseif($code == 'day_to_send'){
                    $opt[] = array(
                        'option_title' => $label,
                        'option_value' => Mage::helper('core')->formatDate($formData[$code],'medium'),
                    );
                }
                else {
                    $opt[] = array(
                        'option_title' => $label,
                        'option_value' => Mage::helper('core')->htmlEscape($formData[$code]),
                    );
                }
            }
        }
        return $opt;
    }
}