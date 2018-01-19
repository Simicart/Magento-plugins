<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/4/17
 * Time: 10:19 AM
 */
class Simi_Simigiftvoucher_Model_Simiobserver {

    private $__NotRecipientShip = array();

    protected function _getSession() {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getQuote() {
        return $this->_getCart()->getQuote();
    }

    /*
     * event catalog_product_save_before
     * */
    public function productSavebefore($observer){
        $product = $observer->getProduct();
        if ($product->getTypeId() == 'simigiftvoucher'){
            $product->setVisibility(1);
        }
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
            if (!count($items)){
                $this->clearGiftcardSession($this->_getSession());
            }
            foreach ($items as $item){
                $data = $item->getData();
                if ($data['product_type'] == 'simigiftvoucher') {
                    $count++;
                }
            }
            $EnableGiftcard = Mage::helper('simigiftvoucher')->getInterfaceCheckoutConfig('show_gift_card');
            if (!$EnableGiftcard){
                $detail_list['gift_card']['use_giftcard'] = false;
            }
            elseif ($count == count($items)){
                $detail_list['gift_card']['use_giftcard'] = false;
                $detail_list['gift_card']['label'] = Mage::helper('simigiftvoucher')->__('Gift Cards cannot be used to purchase Gift Card products');
            }else {
                $detail_list['gift_card']['use_giftcard'] = true;
            }

            if (!$this->checkGiftVoucher()){
                $detail_list['gift_card']['use_giftcard'] = false;
            }

//            if (isset($detail_list['total']['grand_total']) && !$detail_list['grand_total']){
//                $detail_list['gift_card']['use_giftcard'] = false;
//            }elseif (isset($detail_list['total']['grand_total_excl_tax']) && !$detail_list['total']['grand_total_excl_tax']){
//                $detail_list['gift_card']['use_giftcard'] = false;
//            }elseif (isset($detail_list['total']['grand_total_incl_tax']) && !$detail_list['total']['grand_total_incl_tax']){
//                $detail_list['gift_card']['use_giftcard'] = false;
//            }

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
                    $list_code = Mage::getModel('simigiftvoucher/simimapping')->getExistedGiftCard();
                    $detail_list['gift_card']['customer']['list_code'] = $list_code;
                }
                if ($this->getUseGiftVoucher()){
                    $detail_list['gift_card']['giftcode']['use_giftcode'] = 1;
                }
                else {
                    $detail_list['gift_card']['giftcode']['use_giftcode'] = 0;
                }
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

            $quoteitems_detail = array();
            $quoteitems = $detail_list['quoteitems'];
            $showImage = Mage::helper('simigiftvoucher')->getInterfaceCheckoutConfig('display_image_item');
            foreach ($quoteitems as $item){
                if ($item['product_type'] == 'simigiftvoucher'){
                    $item['option'] = $this->getGiftcardOptions($item['item_id']);
                    if ($showImage){
                        $item['image'] = $this->getGiftcardOptions($item['item_id'], true);
                    }
                }else {
                    $this->__NotRecipientShip['no'] = 1;
                }
                $quoteitems_detail[] = $item;
            }
            $this->_getSession()->setNotShipping(1);
            if (isset($this->__NotRecipientShip['no']) && $this->__NotRecipientShip['no']){
                $this->_getSession()->setNotShipping(0);
            }
            $detail_list['quoteitems'] =  $quoteitems_detail;
            $quoteItemApi->detail_list = $detail_list;

            //echo json_encode($this->_getSession()->getData());die;
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
        $EnableGiftcard = Mage::getStoreConfig('simigiftvoucher/interface_payment/show_gift_card');
        if (!$EnableGiftcard){
            $detail_list['gift_card']['use_giftcard'] = false;
        }
        elseif ($count == count($items)){
            $detail_onepage['order']['gift_card']['use_giftcard'] = false;
            $detail_onepage['order']['gift_card']['label'] = Mage::helper('simigiftvoucher')->__('Gift Cards cannot be used to purchase Gift Card products');
        }else {
            $detail_onepage['order']['gift_card']['use_giftcard'] = true;
        }

        if (!$this->checkGiftVoucher()){
            $detail_onepage['order']['gift_card']['use_giftcard'] = false;
        }
//        if (isset($detail_onepage['order']['total']['grand_total']) && !$detail_onepage['order']['total']['grand_total']){
//            $detail_onepage['order']['gift_card']['use_giftcard'] = false;
//        }elseif (isset($detail_onepage['order']['total']['grand_total_excl_tax']) && !$detail_onepage['order']['total']['grand_total_excl_tax']){
//            $detail_onepage['order']['gift_card']['use_giftcard'] = false;
//        }elseif (isset($detail_onepage['order']['total']['grand_total_incl_tax']) && !$detail_onepage['order']['total']['grand_total_incl_tax']){
//            $detail_onepage['order']['gift_card']['use_giftcard'] = false;
//        }

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
                $list_code = Mage::getModel('simigiftvoucher/simimapping')->getExistedGiftCard();
                $detail_onepage['order']['gift_card']['customer']['list_code'] = $list_code;

            }
            if ($this->getUseGiftVoucher()){
                $detail_onepage['order']['gift_card']['giftcode']['use_giftcode'] = 1;
            }
            else {
                $detail_onepage['order']['gift_card']['giftcode']['use_giftcode'] = 0;
            }
            $discounts = $this->getGiftVoucherDiscount();
            if (count($discounts)){
                foreach ($discounts as $code => $discount){
                    $detail_onepage['order']['gift_card']['giftcode'][] = array(
                        "gift_code"  => $code,
                        "hidden_code"  =>  Mage::helper('simigiftvoucher')->getHiddenCode($code),
                        "amount"    =>  $discount
                    );
                }
            }
        }
        if (!$detail_onepage['order']['gift_card']['use_giftcard']){
            if ($this->_getSession()->getNotShipping()){
                $shipping = [];
                $detail_onepage['order']['shipping_address'] = (object) $shipping;
                $detail_onepage['order']['shipping'] = (object) $shipping;
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
        if ($giftcodes){
            foreach ($giftcodes as $key => $code){
                if ($code){
                    $orderTotalHelper->addCustomRow(Mage::helper('simigiftvoucher')->__("Gift Code ($code)"),9,$codediscount[$key]);
                }
            }
        }
        if ($creditDiscount){
            $orderTotalHelper->addCustomRow(Mage::helper('simigiftvoucher')->__('Giftvoucher Credit'),8,$creditDiscount);
        }

    }

    public  function checkGiftVoucher(){
        $session = $this->_getSession();
        $quote = $this->_getQuote();
        if ($quote->getBaseGrandTotal() < 0.0001 && !$session->getSimiuseGiftCard() && !$session->getSimiuseGiftCardCredit()){
            return false;
        }
        return true;
    }

    /**
     * Check customer use gift credit to checkout
     *
     * @return boolean
     */
    public function getUseGiftCredit()
    {
        return $this->_getSession()->getSimiuseGiftCardCredit();
    }

    /**
     * @return string
     */
    public function getUsingAmount()
    {
        return Mage::app()->getStore()->formatPrice(
            $this->_getSession()->getSimiuseGiftCreditAmount()
        );
    }

    /**
     * @return mixed
     */
    public function getSimiuseGiftCreditAmount()
    {
        return $this->_getSession()->getSimiuseGiftCreditAmount();
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
        return $this->_getSession()->getSimiuseGiftCard();
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
        if (!$formData['recipient_ship'] || empty($formData['recipient_ship']) || !isset($formData['recipient_ship'])){
            $this->__NotRecipientShip['yes'] = 1;
        }else {
            $this->__NotRecipientShip['no'] = 1;
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

    public function clearGiftcardSession($session) {
        if ($session->getSimiuseGiftCard())
            $session->setSimiuseGiftCard(null)
                ->setSimigiftCodes(null)
                ->setSimibaseAmountUsed(null)
                ->setSimibaseGiftVoucherDiscount(null)
                ->setSimigiftVoucherDiscount(null)
                ->setSimicodesBaseDiscount(null)
                ->setSimicodesDiscount(null)
                ->setSimigiftMaxUseAmount(null);
        if ($session->getSimiuseGiftCardCredit()) {
            $session->setSimiuseGiftCardCredit(null)
                ->setSimimaxCreditUsed(null)
                ->setSimibaseUseGiftCreditAmount(null)
                ->setSimiuseGiftCreditAmount(null);
        }
    }
}