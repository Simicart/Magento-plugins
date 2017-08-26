<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/4/17
 * Time: 1:48 PM
 */
class Simi_Simigiftvoucher_Model_Simimapping extends Mage_Core_Model_Abstract {



    public function loadGiftcode($id,$customer_id){
        $giftcode = Mage::getModel('simigiftvoucher/giftvoucher')->load($id);
        $customer_voucher = Mage::getModel('simigiftvoucher/customervoucher')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id)
            ->addFieldToFilter('voucher_id',$id)
            ->getData();
        if ($customer_voucher){
            return $this->builderQuery = $giftcode;
        }
        else {
            throw new Exception(Mage::helper('simigiftvoucher')->__('Resource cannot callable.'), 6);
        }
    }

    public function getTemplateInProduct($field){
        $templateIds = explode(',',$field);
        $info_template = array();
        foreach ($templateIds as $templateId){
            $template = Mage::getModel('simigiftvoucher/gifttemplate')->load($templateId)->getData();
            if ($template['images']){
                if ($template['design_pattern'] == Simi_Simigiftvoucher_Model_Designpattern::PATTERN_TOP) {
                    $image_url_position = 'top/';
                }
                elseif ($template['design_pattern'] == Simi_Simigiftvoucher_Model_Designpattern::PATTERN_CENTER){
                    $image_url_position = 'center/';
                }
                elseif ($template['design_pattern'] == Simi_Simigiftvoucher_Model_Designpattern::PATTERN_SIMPLE){
                    $image_url_position = 'simple/';
                }
                elseif ($template['design_pattern'] == Simi_Simigiftvoucher_Model_Designpattern::PATTERN_LEFT){
                    $image_url_position = 'left/';
                }
                else {
                    $image_url_position = 'amazon/';
                }
                $template['images'] = $this->ConvertImagesToArray($template['images'],$image_url_position);
            }
            $info_template[] = $template;
        }
        return $info_template;
    }

    public function ConvertImagesToArray($field,$position_img){
        $images = explode(',',$field);
        $info_images = array();
        foreach ($images as $image){
            $url = array();
            $url['image'] = $image;
            $url['url'] = Mage::getBaseUrl('media',array('_secure'=>true)).'simigiftvoucher/template/images/'.$position_img. $image;
            $info_images[] = $url;
        }
        return $info_images;
    }

    public function fomatDropdown($field){
        $dropdown = explode(',',$field);
        $info_dropdown = array();
        foreach ($dropdown as $item){
            $info_dropdown[] = $item;
        }
        return $info_dropdown;
    }

    public function getConditionProduct($product_id){
        $model = Mage::getModel('simigiftvoucher/product')->getCollection()
                ->addFieldToFilter('product_id',$product_id)
                ->getData();
        $conditions = array();
        foreach ($model as $item){
            $item['conditions_serialized'] = unserialize($item['conditions_serialized']);
            $item['actions_serialized'] = unserialize($item['actions_serialized']);
            $conditions[] = $item;
        }
        return $conditions;
    }
    /**
     *  Use gift code from Cart Page
     * */
    public function UseGiftCode($data){
        $data = (array) $data['contents'];
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $result = array();
        if ($quote->getCouponCode() && !Mage::helper('simigiftvoucher')->getGeneralConfig('use_with_coupon')
            && (!$session->getSimiuseGiftCreditAmount() || !$session->getGiftVoucherDiscount())) {
            $result['notice'] = Mage::helper('simigiftvoucher')->__('A coupon code has been used. You cannot apply gift codes with the coupon to get discount.');
            return $result;
        }

        if ($data['giftvoucher']){
            $session->setSimiuseGiftCard(1);
            $giftcodesAmount = $data['giftcodes'];
            if (count($giftcodesAmount)) {
                $giftMaxUseAmount = unserialize($session->getSimigiftMaxUseAmount());
                if (!is_array($giftMaxUseAmount)) {
                    $giftMaxUseAmount = array();
                }
                $giftMaxUseAmount = array_merge($giftMaxUseAmount, $giftcodesAmount);
                $session->setSimigiftMaxUseAmount(serialize($giftMaxUseAmount));
            }

            $addcodes = array();
            if ($data['existed_giftcode']){
                $addcodes[] = trim($data['existed_giftcode']);
            }
            if ($data['giftcode']){
                $addcodes[] = trim($data['giftcode']);
            }

            if (count($addcodes)){
                if (Mage::helper('simigiftvoucher')->isAvailableToAddCode()) {
                    foreach ($addcodes as $code) {
                        $giftVoucher = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
                        $quote = Mage::getSingleton('checkout/session')->getQuote();
                        if (!$giftVoucher->getId() || ($giftVoucher->getSetId() > 0)) {
                            $codes = Mage::getSingleton('simigiftvoucher/session')->getCodes();
                            $codes[] = $code;
                            Mage::getSingleton('simigiftvoucher/session')->setCodes(array_unique($codes));
                            $error = Mage::helper('simigiftvoucher')->__('Gift card "%s" is invalid.', $code);
                            $max = Mage::helper('simigiftvoucher')->getGeneralConfig('maximum');
                            if ($max - count($codes)) {
                                $error .= Mage::helper('simigiftvoucher')->__('You have %d time(s) remaining to re-enter your Gift Card code.',
                                        $max - count($codes));
                            }
                            //$result['error'] = $error;
                            throw new Exception($error);
                        } else if ($giftVoucher->getBaseBalance() > 0
                            && $giftVoucher->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE
                        ) {
                            if (Mage::helper('simigiftvoucher')->canUseCode($giftVoucher)) {
                                $flag = false;
                                foreach ($quote->getAllItems() as $item) {
                                    if ($giftVoucher->getActions()->validate($item)) {
                                        $flag = true;
                                    }
                                }

                                $giftVoucher->addToSession($session);
                                if ($giftVoucher->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId()
                                    && $giftVoucher->getRecipientName() && $giftVoucher->getRecipientEmail()
                                    && $giftVoucher->getCustomerId()
                                ) {
                                    $result['notice'] = Mage::helper('simigiftvoucher')->__('Please note that gift code "%s" has been sent to your friend. When using, both you and your friend will share the same balance in the gift code.', Mage::helper('simigiftvoucher')->getHiddenCode($code));
                                }

                                $quote->setTotalsCollectedFlag(false)->collectTotals();
                                if ($flag && $giftVoucher->validate($quote->setQuote($quote))) {
                                    $isGiftVoucher = true;
                                    foreach ($quote->getAllItems() as $item) {
                                        if ($item->getProductType() != 'simigiftvoucher') {
                                            $isGiftVoucher = false;
                                            break;
                                        }
                                    }
                                    if (!$isGiftVoucher) {
                                        $result['success'] = Mage::helper('simigiftvoucher')->__('Gift code "%s" has been applied successfully.',
                                                Mage::helper('simigiftvoucher')->getHiddenCode($code));
                                    } else {
                                        $result['notice'] = Mage::helper('simigiftvoucher')->__('Please remove your Gift Card information since you cannot use either gift codes or Gift Card credit balance to purchase other Gift Card products.');
                                    }
                                }
                                else {
                                    $error = Mage::helper('simigiftvoucher')->__('You can’t use this gift code since its conditions haven’t been met. Please check these conditions by entering your gift code.');
                                    throw new Exception($error);
                                }
                            }
                            else {
                                $error = Mage::helper('simigiftvoucher')->__('This gift code limits the number of users',
                                    Mage::helper('simigiftvoucher')->getHiddenCode($code));
                                throw new Exception($error);
                            }
                        } else {
                            $error = Mage::helper('simigiftvoucher')->__('Gift code "%s" is no longer available to use.', $code);
                            throw new Exception($error);
                        }
                    }
                }
                else {
                    $error = Mage::helper('simigiftvoucher')->__('The maximum number of times to enter gift codes is %d!',Mage::helper('simigiftvoucher')->getGeneralConfig('maximum'));
                    throw new Exception($error);
                }
            }
            else {
                $result['success'] = Mage::helper('simigiftvoucher')->__('Your Gift Card(s) has been applied successfully.');
            }
        }
        elseif ($session->getSimiuseGiftCard()) {
            $session->setSimiuseGiftCard(null);
            $result['success'] = Mage::helper('simigiftvoucher')->__('Your Gift Card information has been removed successfully.');
        }
        return $result;
    }

    /**
     *  Use credit from Cart Page
     * */
    public function UseCredit($data){
        $data = (array) $data['contents'];
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $info = array();
        if($data['usecredit'] && Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit') && $data['credit_amount']){
            $session->setSimiuseGiftCardCredit(1);
            $session->setSimimaxCreditUsed(floatval($data['credit_amount']));
            $quote->setTotalsCollectedFlag(false)->collectTotals();
            $info['success'] = Mage::helper('simigiftvoucher')->__('Your Credit has been used successfully.');
        }else {
            $session->setSimiuseGiftCardCredit(0);
            $session->setSimimaxCreditUsed(null);
            $info['success'] = Mage::helper('simigiftvoucher')->__('Your Credit information has been removed successfully.');
        }
        return $info;
    }

    /**
     *  Remove giftcode from Cart Page
     * */
    public function removeGiftCode($data){
        $data = (array) $data['contents'];
        $session = Mage::getSingleton('checkout/session');
        $code = trim($data['giftcode']);
        $codes = trim($session->getSimigiftCodes());
        $success = false;

        if ($code && $codes) {
            $codesArray = explode(',', $codes);
            foreach ($codesArray as $key => $value) {
                if ($value == $code) {
                    unset($codesArray[$key]);
                    $success = true;
                    $giftMaxUseAmount = unserialize($session->getSimigiftMaxUseAmount());
                    if (is_array($giftMaxUseAmount) && array_key_exists($code, $giftMaxUseAmount)) {
                        unset($giftMaxUseAmount[$code]);
                        $session->setSimigiftMaxUseAmount(serialize($giftMaxUseAmount));
                    }
                    break;
                }
            }
        }

        if ($success) {
            $codes = implode(',', $codesArray);
            $session->setSimigiftCodes($codes);
            $result['success'] =  Mage::helper('simigiftvoucher')->__('Gift Card "%s" has been removed successfully!',
                Mage::helper('simigiftvoucher')->getHiddenCode($code));
        } else {
            $error =  Mage::helper('simigiftvoucher')->__('Gift card "%s" not found!', $code);
            throw new Exception($error);
        }

        return $result;
    }

    /**
     * Use credit from Checkout page
     * */
    public function UseCreditCheckout($data){
        $data = (array) $data['contents'];
        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit')) {
            return;
        }
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $session->setSimiuseGiftCardCredit($data['usecredit']);

        $result = array();
        if ($quote->getCouponCode() && !Mage::helper('simigiftvoucher')->getGeneralConfig('use_with_coupon')
            && (!$session->getSimiuseGiftCreditAmount() || !$session->getGiftVoucherDiscount())) {
            $result['notice'] = Mage::helper('simigiftvoucher')->__('A coupon code has been used. You cannot apply Gift Card credit with the coupon to get discount.');
        } else {
            $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
            $quote->collectTotals()->save();
            if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
                $result['updatepayment'] = 1;
            }
            if ($data['usecredit'] == 1){
                $result['success'] = Mage::helper('simigiftvoucher')->__('Use Your Balance Credit !');
            }
            else {
                $result['success'] = Mage::helper('simigiftvoucher')->__('Not Use Your Balance Credit !');
            }
        }
        //zend_debug::dump($result);die;
        return $result;
    }

    /**
     * Update the amount of Gift Card credit
     */
    public function creditamountAction($data)
    {
        $data = (array) $data['contents'];
        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit')) {
            return;
        }
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $result = array();

        if ($quote->getCouponCode() && !Mage::helper('simigiftvoucher')->getGeneralConfig('use_with_coupon')
            && (!$session->getSimiuseGiftCreditAmount() || !$session->getGiftVoucherDiscount())) {
            return Mage::helper('simigiftvoucher')->__('A coupon code has been used. You cannot apply Gift Card credit with the coupon to get discount.');
        } else {
            $amount = floatval($data['credit_amount']);
            if ($amount > -0.0001 && (abs($amount - $session->getSimimaxCreditUsed()) > 0.0001
                    || !$session->getSimimaxCreditUsed())
            ) {
                $session->setSimimaxCreditUsed($amount);
                $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
                $session->getQuote()->collectTotals()->save();
                if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
                    $result['updatepayment'] = 1;
                } else {
                    //$giftVoucherBlock = $this->getLayout()->createBlock('giftvoucher/payment_form');
                    //$result['html'] = $giftVoucherBlock->toHtml();
                }
                $result['success'] = Mage::helper('simigiftvoucher')->__('Amount Your credit has been updated successfully.');
            }
        }
        return $result;
    }



    public function AddRedeem($data){
        $data = (array) $data['contents'];

        if (!Mage::getSingleton('customer/session')->isLoggedIn()){
            throw new Exception(Mage::helper('customer')->__('Please login First.'), 4);
        }
        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit')) {
            throw new Exception(Mage::helper('simigiftvoucher')->__('Please enable credit in configuration.'), 4);
        }
        if ($data['giftcode']){
            $giftcode = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($data['giftcode']);
            if (!$giftcode->getId() || $giftcode->getSetId()){
                throw new Exception(Mage::helper('simigiftvoucher')->__('Gift card "%s" is invalid.',$data['giftcode']), 4);
            } else {
                $conditions = $giftcode->getConditionsSerialized();
                if (!empty($conditions)) {
                    $conditions = unserialize($conditions);
                    if (is_array($conditions) && !empty($conditions)){
                        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('credit_condition') && isset($conditions['conditions'])
                            && $conditions['conditions']) {
                            throw new Exception(Mage::helper('simigiftvoucher')->__('Gift code "%s" has usage conditions, you cannot redeem it to Gift Card credit.',$data['giftcode']), 4);
                        }
                    }
                }

                $actions = $giftcode->getActionsSerialized();
                if (!empty($actions)){
                    $actions = unserialize($actions);
                    if (is_array($actions) && !empty($actions)){
                        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('credit_condition') && isset($actions['conditions'])
                            && $actions['conditions']) {
                            throw new Exception(Mage::helper('simigiftvoucher')->__('Gift code "%s" has usage conditions, you cannot redeem it to Gift Card credit.',$data['giftcode']), 4);
                        }
                    }
                }

                if(!Mage::helper('simigiftvoucher')->canUseCode($giftcode)){
                    throw new Exception(Mage::helper('simigiftvoucher')->__('The gift code usage has exceeded the number of users allowed.'), 4);
                }

                $customer = Mage::getSingleton('customer/session')->getCustomer();
                if ($giftcode->getBalance() == 0) {
                    throw new Exception(Mage::helper('simigiftvoucher')->__('%s - The current balance of this gift code is 0.',$data['giftcode']), 4);
                }

                if ($giftcode->getStatus() != 2 && $giftcode->getStatus() != 4) {
                    throw new Exception(Mage::helper('simigiftvoucher')->__('Gift code "%s" is not avaliable.',$data['giftcode']), 4);
                } else {
                    $balance = $giftcode->getBalance();

                    $credit = Mage::getModel('simigiftvoucher/credit')->getCreditAccountLogin();
                    $creditCurrencyCode = $credit->getCurrency();
                    $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
                    if (!$creditCurrencyCode) {
                        $creditCurrencyCode = $baseCurrencyCode;
                        $credit->setCurrency($creditCurrencyCode);
                        $credit->setCustomerId($customer->getId());
                    }

                    $voucherCurrency = Mage::getModel('directory/currency')->load($giftcode->getCurrency());
                    $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
                    $creditCurrency = Mage::getModel('directory/currency')->load($creditCurrencyCode);

                    $amountTemp = $balance * $balance / $baseCurrency->convert($balance, $voucherCurrency);
                    $amount = $baseCurrency->convert($amountTemp, $creditCurrency);

                    $credit->setBalance($credit->getBalance() + $amount);

                    $credithistory = Mage::getModel('simigiftvoucher/credithistory')
                        ->setCustomerId($customer->getId())
                        ->setAction('Redeem')
                        ->setCurrencyBalance($credit->getBalance())
                        ->setGiftcardCode($giftcode->getGiftCode())
                        ->setBalanceChange($balance)
                        ->setCurrency($giftcode->getCurrency())
                        ->setCreatedDate(now());

                    $history = Mage::getModel('simigiftvoucher/history')->setData(array(
                        'order_increment_id' => '',
                        'giftvoucher_id' => $giftcode->getId(),
                        'created_at' => now(),
                        'action' => Simi_Simigiftvoucher_Model_Actions::ACTIONS_REDEEM,
                        'amount' => $balance,
                        'balance' => 0.0,
                        'currency' => $giftcode->getCurrency(),
                        'status' => $giftcode->getStatus(),
                        'order_amount' => '',
                        'comments' => Mage::helper('simigiftvoucher')->__('Redeem to Gift Card credit balance'),
                        'extra_content' => Mage::helper('simigiftvoucher')->__('Redeemed by %s', $customer->getName()),
                        'customer_id' => $customer->getId(),
                        'customer_email' => $customer->getEmail(),
                    ));

                    try {
                        $giftcode->setBalance(0)->setStatus(Simi_Simigiftvoucher_Model_Status::STATUS_USED)->save();
                    } catch (Exception $e) {
                        throw new Exception(Mage::helper('simigiftvoucher')->__($e->getMessage()), 4);
                    }

                    try {
                        $credit->save();
                    } catch (Exception $e) {
                        $giftcode->setBalance($balance)
                            ->setStatus(Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE)
                            ->save();
                        throw new Exception(Mage::helper('simigiftvoucher')->__($e->getMessage()), 4);
                    }
                    try {
                        $history->save();
                        $credithistory->save();
                    } catch (Exception $e) {
                        $giftcode->setBalance($balance)
                            ->setStatus(Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE)
                            ->save();
                        $credit->setBalance($credit->getBalance() - $amount)->save();
                        throw new Exception(Mage::helper('simigiftvoucher')->__($e->getMessage()), 4);
                    }
                    return Mage::helper('simigiftvoucher')->__('Gift card "%s" was successfully redeemed',$data['giftcode']);
                }
            }
        }else {
            throw new Exception(Mage::helper('simigiftvoucher')->__('Please enter gift code.'), 4);
        }
    }

    /**
     *  Add gift code in my list
     * */
    public function AddMyList($data){
        $data = (array) $data['contents'];
        if (!Mage::getSingleton('customer/session')->isLoggedIn()){
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current' => true)));
            throw new Exception(Mage::helper('customer')->__('Please login First.'), 4);
        }

        $code = $data['giftcode'];
        $max = Mage::helper('simigiftvoucher')->getGeneralConfig('maximum');

        if ($code){
            $giftcode = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
            if (!$giftcode->getId() || $giftcode->getSetId()){
                throw new Exception(Mage::helper('simigiftvoucher')->__('Gift card "%s" is invalid.',$code), 4);
            }
            else {
                if(!Mage::helper('simigiftvoucher')->canUseCode($giftcode)){
                  throw  new Exception(Mage::helper('simigiftvoucher')->__('The gift code usage has exceeded the number of users allowed'),4);
                }

                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $collection = Mage::getModel('simigiftvoucher/customervoucher')->getCollection();
                $collection->addFieldToFilter('customer_id', $customer->getId())
                    ->addFieldToFilter('voucher_id', $giftcode->getId());
                if ($collection->getSize()){
                    throw new Exception(Mage::helper('simigiftvoucher')->__('This gift code has already existed in your list.'),4);
                }
                elseif ($giftcode->getStatus() != 1 && $giftcode->getStatus() != 2 && $giftcode->getStatus() != 4){
                    throw new Exception(Mage::helper('simigiftvoucher')->__('Gift code "%s" is not available', $code),4);
                }
                else {
                    $model = Mage::getModel('simigiftvoucher/customervoucher')
                        ->setCustomerId($customer->getId())
                        ->setVoucherId($giftcode->getId())
                        ->setAddedDate(now());
                    try{
                        $model->save();
                    }
                    catch (Exception $e){
                        throw new Exception(Mage::helper('simigiftvoucher')->__($e->getMessage()), 4);
                    }
                    return Mage::helper('simigiftvoucher')->__('The gift code has been added to your list successfully.');
                }
            }
        }
    }

    /**
     * get existed gift Card
     *
     * @return array
     */
    public function getExistedGiftCard()
    {
        $session = Mage::getSingleton('customer/session');
        if (!$session->isLoggedIn()) {
            return array();
        }
        $customerId = $session->getCustomer()->getId();
        $storeId = Mage::app()->getStore()->getStoreId();
        zend_debug::dump($storeId);
        $collection = Mage::getResourceModel('simigiftvoucher/customervoucher_collection')
            ->addFieldToFilter('main_table.customer_id', $customerId);
        $voucherTable = $collection->getTable('simigiftvoucher/giftvoucher');
        $collection->getSelect()
            ->join(array('v' => $voucherTable), 'main_table.voucher_id = v.giftvoucher_id', array(
                    'gift_code', 'balance', 'currency', 'conditions_serialized', 'store_id')
            )->where('v.status = ?', Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE)
            ->where("v.recipient_name IS NULL OR v.recipient_name = '' OR (v.customer_email <> ?)", $session->getCustomer()->getEmail())
            ->where("v.set_id IS NULL OR v.set_id <= 0 ")
        ;
        $giftCards = array();
        $addedCodes = array();
        if ($codes = Mage::getSingleton('checkout/session')->getSimigiftCodes()) {
            $addedCodes = explode(',', $codes);
        }

        $helper = Mage::helper('simigiftvoucher');
        $conditions = Mage::getSingleton('simigiftvoucher/giftvoucher')->getConditions();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quote->setQuote($quote);
        foreach ($collection as $item) {
            $itemId = $item->getStoreId();
            if ($itemId != 0 && $itemId != $storeId) {
                continue;
            }
            if (in_array($item->getGiftCode(), $addedCodes)) {
                continue;
            }
            if ($item->getConditionsSerialized()) {
                $conditionsArr = unserialize($item->getConditionsSerialized());
                if (!empty($conditionsArr) && is_array($conditionsArr)) {
                    $conditions->setConditions(array())->loadArray($conditionsArr);
                    if (!$conditions->validate($quote)) {
                        continue;
                    }
                }
            }
            $giftCards[] = array(
                'store_id' => $item->getStoreId(),
                'gift_code' => $item->getGiftCode(),
                'hidden_code' => $helper->getHiddenCode($item->getGiftCode()),
                'balance' => $this->getGiftCardBalance($item)
            );

        }
        return $giftCards;
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
     *  Get list code customer
     * */
    public function getListCode(){
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $timezone = ((Mage::app()->getLocale()->date()->get(Zend_Date::TIMEZONE_SECS)) / 3600);
        $collection = Mage::getModel('simigiftvoucher/customervoucher')->getCollection()
            ->addFieldToFilter('main_table.customer_id', $customerId);
        $voucherTable = Mage::getModel('core/resource')->getTableName('simigiftvoucher');
        $collection->getSelect()
            ->joinleft(
                array('voucher_table' => $voucherTable), 'main_table.voucher_id = voucher_table.giftvoucher_id', array(
                'recipient_name',
                'gift_code',
                'balance',
                'currency',
                'status',
                'expired_at',
                'customer_check_id' => 'voucher_table.customer_id',
                'recipient_email',
                'customer_email'
            ))
            ->where('voucher_table.status <> ?', Simi_Simigiftvoucher_Model_Status::STATUS_DELETED);
        $collection->getSelect()
            ->columns(array(
                'added_date' => new Zend_Db_Expr("SUBDATE(added_date,INTERVAL " . $timezone . " HOUR)"),
            ));
        $collection->getSelect()
            ->columns(array(
                'expired_at' => new Zend_Db_Expr("SUBDATE(expired_at,INTERVAL " . $timezone . " HOUR)"),
            ));
        $collection->setOrder('customer_voucher_id', 'DESC');

        $data = $collection->getData();
        $result = array();
        foreach ($data as $item){
            $item['added_date'] = Mage::helper('core')->formatDate($item['added_date'],'medium');
            $item['expired_at'] = Mage::helper('core')->formatDate($item['expired_at'],'medium');
            $item['currency_symbol'] = Mage::app()->getLocale()->currency($item['currency'])->getSymbol();
            $result[] = $item;
        }
        return $result;
    }

    public function getAction($id){
        $result = array();
        $giftvoucher = Mage::getModel('simigiftvoucher/giftvoucher')->load($id);
        if ($giftvoucher->getStatus() < Simi_Simigiftvoucher_Model_Status::STATUS_DISABLED){
            if ($giftvoucher->getRecipientName() && $giftvoucher->getRecipientEmail() && ($giftvoucher->getCustomerId()
                    == Mage::getSingleton('customer/session')->getCustomerId() || $giftvoucher->getCustomerEmail()
                    == Mage::getSingleton('customer/session')->getCustomer()->getEmail()) &&
                (!(($giftvoucher->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_PENDING) && ($giftvoucher->getSetId() > 0)))
            )
            {
                $result[] = Mage::helper('simigiftvoucher')->__('Email');
            }
        }

        $avaiable = Mage::helper('simigiftvoucher')
            ->canUseCode($giftvoucher);
        if (Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit') && $avaiable) {
            if (($giftvoucher->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE
                    || ($giftvoucher->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_USED
                        && $giftvoucher->getBalance() > 0)) && (!($giftvoucher->getSetId() > 0)))
            {
                $result[] = Mage::helper('simigiftvoucher')->__('Redeem');
            }
        }
        $result[] = Mage::helper('simigiftvoucher')->__('Remove');
        return $result;
    }

    /**
     * Get the price information of Gift Card product
     *
     * @param  $product
     * @return array
     */
    public function getGiftAmount($product)
    {
        $giftValue = Mage::helper('simigiftvoucher/giftproduct')->getGiftValue($product);
        $store = Mage::app()->getStore();
        switch ($giftValue['type_value']) {
            case 'range':
                $giftValue['from'] = $this->convertPrice($product, $giftValue['from']);
                $giftValue['to'] = $this->convertPrice($product, $giftValue['to']);
                //$giftValue['from_txt'] = $store->formatPrice($giftValue['from'],false);
                //$giftValue['to_txt'] = $store->formatPrice($giftValue['to'],false);
                break;
            case 'dropdown':
                $giftValue['options_value'] = $this->_convertPrices($product, $giftValue['options_value']);
                $giftValue['prices_dropdown'] = $this->_convertPrices($product, $giftValue['prices_dropdown']);
                $giftValue['prices_dropdown'] = array_combine($giftValue['options_value'], $giftValue['prices_dropdown']);
                //$giftValue['options_txt'] = $this->_formatPrices($giftValue['options']);
                break;
            case 'fixed':
                $giftValue['value'] = $this->convertPrice($product, $giftValue['value']);
                //$giftValue['value_txt'] = $store->formatPrice($giftValue['value']);
                $giftValue['price'] = $this->convertPrice($product, $giftValue['price']);
                break;
            default:
                $giftValue['type_value'] = 'any';
        }
        return $giftValue;
    }

    /**
     * Convert Gift Card base price
     *
     * @param  $product
     * @param float $basePrices
     * @return float
     */
    protected function _convertPrices($product, $basePrices)
    {
        foreach ($basePrices as $key => $price) {
            $basePrices[$key] = $this->convertPrice($product, $price);
        }
        return $basePrices;
    }

    /**
     * Get Gift Card product price with all tax settings processing
     *
     * @param  $product
     * @param float $price
     * @return float
     */
    public function convertPrice($product, $price)
    {
        $includeTax = ( Mage::getStoreConfig('tax/display/type') != 1 );
        $store = Mage::app()->getStore();

        $priceWithTax = Mage::helper('tax')->getPrice($product, $price, $includeTax);
        return $store->convertPrice($priceWithTax);
    }

    /**
     * Formatted Gift Card price
     *
     * @param array $prices
     * @return array
     */
    protected function _formatPrices($prices)
    {
        $store = Mage::app()->getStore();
        foreach ($prices as $key => $price) {
            $prices[$key] = $store->formatPrice($price, false);
        }
        return $prices;
    }

    /**
     * Add gift codes from Checkout page
     */
    public function addgiftCheckout($data)
    {
        $data = (array) $data['contents'];
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $result = array();

        if ($quote->getCouponCode() && !Mage::helper('simigiftvoucher')->getGeneralConfig('use_with_coupon')
            && (!$session->getSimiuseGiftCreditAmount() || !$session->getGiftVoucherDiscount())) {
            $result['notice'] = Mage::helper('simigiftvoucher')->__('A coupon code has been used. You cannot apply gift codes with the coupon to get discount.');
        }
        else {
            $addcodes = array();
            if ($data['existed_giftcode']){
                $addcodes[] = trim($data['existed_giftcode']);
            }
            if ($data['giftcode']){
                $addcodes[] = trim($data['giftcode']);
            }

            if (count($addcodes)){
                if (Mage::helper('simigiftvoucher')->isAvailableToAddCode()) {
                    $quote->setTotalsCollectedFlag(false)->collectTotals()->save();
                    foreach ($addcodes as $code) {
                        $giftVoucher = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
                        $quote = Mage::getSingleton('checkout/session')->getQuote();
                        if (!$giftVoucher->getId() || ($giftVoucher->getSetId() > 0)) {
                            $codes = Mage::getSingleton('simigiftvoucher/session')->getCodes();
                            $codes[] = $code;
                            Mage::getSingleton('simigiftvoucher/session')->setCodes(array_unique($codes));
                            $error = Mage::helper('simigiftvoucher')->__('Gift card "%s" is invalid.', $code);
                            $max = Mage::helper('simigiftvoucher')->getGeneralConfig('maximum');
                            if ($max - count($codes)) {
                                $error .= Mage::helper('simigiftvoucher')->__('You have %d time(s) remaining to re-enter your Gift Card code.',
                                    $max - count($codes));
                            }
                            //$result['error'] = $error;
                            throw new Exception($error);
                        } else if ($giftVoucher->getBaseBalance() > 0
                            && $giftVoucher->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE
                        ) {
                            if (Mage::helper('simigiftvoucher')->canUseCode($giftVoucher)) {
                                $flag = false;
                                foreach ($quote->getAllItems() as $item) {
                                    if ($giftVoucher->getActions()->validate($item)) {
                                        $flag = true;
                                    }
                                }

                                $giftVoucher->addToSession($session);
                                if ($giftVoucher->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId()
                                    && $giftVoucher->getRecipientName() && $giftVoucher->getRecipientEmail()
                                    && $giftVoucher->getCustomerId()
                                ) {
                                    $result['notice'] = Mage::helper('simigiftvoucher')->__('Please note that gift code "%s" has been sent to your friend. When using, both you and your friend will share the same balance in the gift code.', Mage::helper('simigiftvoucher')->getHiddenCode($code));
                                }

                                $quote->setTotalsCollectedFlag(false)->collectTotals();
                                if ($flag && $giftVoucher->validate($quote->setQuote($quote))) {
                                    $isGiftVoucher = true;
                                    foreach ($quote->getAllItems() as $item) {
                                        if ($item->getProductType() != 'simigiftvoucher') {
                                            $isGiftVoucher = false;
                                            break;
                                        }
                                    }
                                    if (!$isGiftVoucher) {
                                        $result['success'] = Mage::helper('simigiftvoucher')->__('Gift code "%s" has been applied successfully.',
                                            Mage::helper('simigiftvoucher')->getHiddenCode($code));
                                    } else {
                                        $result['notice'] = Mage::helper('simigiftvoucher')->__('Please remove your Gift Card information since you cannot use either gift codes or Gift Card credit balance to purchase other Gift Card products.');
                                    }
                                }
                                else {
                                    $error = Mage::helper('simigiftvoucher')->__('You can’t use this gift code since its conditions haven’t been met. Please check these conditions by entering your gift code.');
                                    throw new Exception($error);
                                }
                            }
                            else {
                                $error = Mage::helper('simigiftvoucher')->__('This gift code limits the number of users',
                                    Mage::helper('simigiftvoucher')->getHiddenCode($code));
                                throw new Exception($error);
                            }
                        } else {
                            $error = Mage::helper('simigiftvoucher')->__('Gift code "%s" is no longer available to use.', $code);
                            throw new Exception($error);
                        }
                    }
                }
                else {
                    $error = Mage::helper('simigiftvoucher')->__('The maximum number of times to enter gift codes is %d!',Mage::helper('simigiftvoucher')->getGeneralConfig('maximum'));
                    throw new Exception($error);
                }
            }
            else {
                $result['success'] = Mage::helper('simigiftvoucher')->__('Your Gift Card(s) has been applied successfully.');
            }
        }
        return $result;
    }

    /**
     * Update the amount of gift codes
     */
    public function updateAmountGiftcode($data)
    {
        $data = (array) $data['contents'];
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $codes = $session->getSimigiftCodes();

        $code = $data['giftcode'];
        $amount = floatval($data['amount']);
        $result = array();
        if ($quote->getCouponCode() && !Mage::helper('simigiftvoucher')->getGeneralConfig('use_with_coupon')
            && (!$session->getSimiuseGiftCreditAmount() || !$session->getGiftVoucherDiscount())) {
            $result['notice'] = Mage::helper('simigiftvoucher')->__('A coupon code has been used. You cannot apply Gift Card credit with the coupon to get discount.');
        } else {
            if (in_array($code, explode(',', $codes))) {
                $giftMaxUseAmount = unserialize($session->getSimigiftMaxUseAmount());
                if (!is_array($giftMaxUseAmount)) {
                    $giftMaxUseAmount = array();
                }
                $giftMaxUseAmount[$code] = $amount;
                $session->setSimigiftMaxUseAmount(serialize($giftMaxUseAmount));
                $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
                $quote->collectTotals()->save();
                if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
                    $result['updatepayment'] = 1;
                } else {
                    //$giftVoucherBlock = $this->getLayout()->createBlock('giftvoucher/payment_form');
                    //$result['html'] = $giftVoucherBlock->toHtml();
                }
                $result['success'] = Mage::helper('simigiftvoucher')->__('Amount gift code "%s" changed successfully!.', $code);
            } else {
                $result['error'] = Mage::helper('simigiftvoucher')->__('Gift card "%s" is not added.', $code);
                throw new Exception($result['error'],4);
            }
        }
        return $result;
    }

    /**
     * Remove gift codes from Checkout page
     */
    public function removeCodeCheckout($data)
    {
        $data = (array) $data['contents'];
        $session = Mage::getSingleton('checkout/session');
        $code = trim($data['giftcode']);
        $codes = $session->getSimigiftCodes();

        $success = false;
        if ($code && $codes) {
            $codesArray = explode(',', $codes);
            foreach ($codesArray as $key => $value) {
                if ($value == $code) {
                    unset($codesArray[$key]);
                    $success = true;
                    $giftMaxUseAmount = unserialize($session->getSimigiftMaxUseAmount());
                    if (is_array($giftMaxUseAmount) && array_key_exists($code, $giftMaxUseAmount)) {
                        unset($giftMaxUseAmount[$code]);
                        $session->setSimigiftMaxUseAmount(serialize($giftMaxUseAmount));
                    }
                    break;
                }
            }
        }

        $result = array();
        if ($success) {
            $codes = implode(',', $codesArray);
            $session->setSimigiftCodes($codes);
            $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
            $session->getQuote()->collectTotals()->save();
            if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
                $result['updatepayment'] = 1;
            } else {
                //$giftVoucherBlock = $this->getLayout()->createBlock('giftvoucher/payment_form');
                //$result['html'] = $giftVoucherBlock->toHtml();
            }
            $result['success'] = Mage::helper('simigiftvoucher')->__('Gift card "%s" is removed successfully.', $code);
        } else {
            $result['error'] = Mage::helper('simigiftvoucher')->__('Gift card "%s" is not found.', $code);
            throw new Exception($result['error'],4);
        }
        return $result;
    }

    /**
     * Change use gift card to spend checkout page
     */
    public function ChangeUseCode($data)
    {
        $data = (array) $data['contents'];
        $session = Mage::getSingleton('checkout/session');
        $session->setSimiuseGiftCard($data['giftvoucher']);
        $result = array();
        $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
        $session->getQuote()->collectTotals()->save();
        if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
            $result['updatepayment'] = 1;
        }
        if ($data['giftvoucher'] == 1 ){
            $result['success'] = Mage::helper('simigiftvoucher')->__('Use Your Gift Code !');
            return $result;
        }
        else {
            $result['success'] = Mage::helper('simigiftvoucher')->__('Not Use Your Gift Code !');
            return $result;
        }

    }


}