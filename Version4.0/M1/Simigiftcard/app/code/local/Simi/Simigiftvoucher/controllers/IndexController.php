<?php
/**
 * Simi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Simicart.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Simicart.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @module     Giftvoucher
 * @author      Simi Developer
 *
 * @copyright   Copyright (c) 2016 Simi (http://www.Simicart.com/)
 * @license     http://www.Simicart.com/license-agreement.html
 *
 */

/**
 * Giftvoucher controller
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * Index action
     */
    public function indexAction()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());
            $this->_redirect("customer/account/login");
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Check number of times entering the gift code
     */
    public function checkAction()
    {
        $this->loadLayout();
        $max = Mage::helper('simigiftvoucher')->getGeneralConfig('maximum');

        if ($code = $this->getRequest()->getParam('code')) {
            $this->getLayout()->getBlock('head')->setTitle(Mage::helper('simigiftvoucher')->getHiddenCode($code));

            $giftVoucher = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
            $codes = Mage::getSingleton('simigiftvoucher/session')->getCodes();
            if (!$giftVoucher->getId()) {
                $codes[] = $code;
                $codes = array_unique($codes);
                Mage::getSingleton('simigiftvoucher/session')->setCodes($codes);
            }

            if (!Mage::helper('simigiftvoucher')->isAvailableToAddCode()) {
                Mage::getSingleton('simigiftvoucher/session')->addError(
                    Mage::helper('simigiftvoucher')->__('The maximum number of times to enter gift codes is %d!', $max));
                $this->_initLayoutMessages('simigiftvoucher/session');
                $this->renderLayout();
                return;
            }

            if (!$giftVoucher->getId()) {
                $errorMessage = Mage::helper('simigiftvoucher')->__('Invalid gift code. ');
                if ($max) {
                    $errorMessage .=
                        Mage::helper('simigiftvoucher')->__('You have %d time(s) remaining to check your Gift Card code.',
                            $max - count($codes));
                }
                Mage::getSingleton('simigiftvoucher/session')->addError($errorMessage);
            }
        } else {
            $this->getLayout()->getBlock('head')->setTitle(Mage::helper('simigiftvoucher')->__('Check Gift Card Balance'));
            if (!Mage::helper('simigiftvoucher')->isAvailableToAddCode()) {
                Mage::getSingleton('simigiftvoucher/session')->addError(
                    Mage::helper('simigiftvoucher')->__('The maximum number of times to enter gift codes is %d!', $max));
            }
        }

        $this->_initLayoutMessages('simigiftvoucher/session');
        $this->renderLayout();
    }

    /**
     * Remove gift codes from customer's account
     */
    public function removeAction()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect("customer/account/login");
            return;
        }
        $customerVoucherId = $this->getRequest()->getParam('id');
        $voucher = Mage::getModel('simigiftvoucher/customervoucher')->load($customerVoucherId);
        if ($voucher->getCustomerId() == Mage::getSingleton('customer/session')->getCustomer()->getId()) {
            try {
                $voucher->delete();
                Mage::getSingleton('core/session')->addSuccess(
                    Mage::helper('simigiftvoucher')->__('Gift card was successfully removed'));
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
        $this->_redirect("simigiftvoucher/index/index");
    }

    /**
     * Redeem display action
     */
    public function addredeemAction()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect("customer/account/login");
            return;
        }
        $this->loadLayout();
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('simigiftvoucher/index/index');
        }
        $this->renderLayout();
    }

    /**
     * Add gift codes to customer's list
     */
    public function addlistAction()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current' => true)));
            $this->_redirect("customer/account/login");
            return;
        }
        $code = $this->getRequest()->getParam('giftvouchercode');

        $max = Mage::helper('simigiftvoucher')->getGeneralConfig('maximum');

        if ($code) {
            $giftVoucher = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
            $codes = Mage::getSingleton('simigiftvoucher/session')->getCodes();
            if (!Mage::helper('simigiftvoucher')->isAvailableToAddCode()) {
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('simigiftvoucher')->__('The maximum number of times to enter gift codes is %d!', $max));
                $this->_redirect("simigiftvoucher/index/index");
                return;
            }
            if (!$giftVoucher->getId() || $giftVoucher->getSetId()) {
                $codes[] = $code;
                $codes = array_unique($codes);
                Mage::getSingleton('simigiftvoucher/session')->setCodes($codes);
                $errorMessage = Mage::helper('simigiftvoucher')->__('Gift card "%s" is invalid.', $code);
                if ($max) {
                    $errorMessage .=
                        Mage::helper('simigiftvoucher')->__('You have %d time(s) remaining to re-enter Gift Card code.',
                            $max - count($codes));
                }
                Mage::getSingleton('core/session')->addError($errorMessage);
                $this->_redirect("simigiftvoucher/index/addredeem");
                return;
            } else {
                if (!Mage::helper('simigiftvoucher')->canUseCode($giftVoucher)) {
                    Mage::getSingleton('core/session')->addError(
                        $this->__('The gift code usage has exceeded the number of users allowed.'));
                    return $this->_redirect("simigiftvoucher/index/index");
                }
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $collection = Mage::getModel('simigiftvoucher/customervoucher')->getCollection();
                $collection->addFieldToFilter('customer_id', $customer->getId())
                    ->addFieldToFilter('voucher_id', $giftVoucher->getId());
                if ($collection->getSize()) {
                    Mage::getSingleton('core/session')->addError(
                        Mage::helper('simigiftvoucher')->__('This gift code has already existed in your list.'));
                    $this->_redirect("simigiftvoucher/index/addredeem");
                    return;
                } elseif ($giftVoucher->getStatus() != 1 && $giftVoucher->getStatus() != 2
                    && $giftVoucher->getStatus() != 4
                ) {
                    Mage::getSingleton('core/session')->addError(
                        Mage::helper('simigiftvoucher')->__('Gift code "%s" is not available', $code));
                    $this->_redirect("simigiftvoucher/index/addredeem");
                    return;
                } else {
                    $model = Mage::getModel('simigiftvoucher/customervoucher')
                        ->setCustomerId($customer->getId())
                        ->setVoucherId($giftVoucher->getId())
                        ->setAddedDate(now());
                    try {
                        $model->save();
                        Mage::getSingleton('core/session')->addSuccess(
                            Mage::helper('simigiftvoucher')->__('The gift code has been added to your list successfully.'));
                        $this->_redirect("simigiftvoucher/index/index");
                        return;
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect("simigiftvoucher/index/addredeem");
                        return;
                    }
                }
            }
        }

        $this->_redirect("simigiftvoucher/index/index");
    }

    /**
     * Redeem gift codes to gift card credit
     */
    public function redeemAction()
    {
        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit')) {
            $this->_redirect("simigiftvoucher/index/index");
            return;
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect("customer/account/login");
            return;
        }
        $code = $this->getRequest()->getParam('giftvouchercode');

        $max = Mage::helper('simigiftvoucher')->getGeneralConfig('maximum');

        if ($code) {
            $giftVoucher = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
            $codes = Mage::getSingleton('simigiftvoucher/session')->getCodes();
            if (!Mage::helper('simigiftvoucher')->isAvailableToAddCode()) {
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('simigiftvoucher')->__('The maximum number of times to enter gift codes is %d!', $max));
                $this->_redirect("simigiftvoucher/index/index");
                return;
            }
            if (!$giftVoucher->getId() || $giftVoucher->getSetId()) {
                $codes[] = $code;
                $codes = array_unique($codes);
                Mage::getSingleton('simigiftvoucher/session')->setCodes($codes);
                $errorMessage = Mage::helper('simigiftvoucher')->__('Gift card "%s" is invalid.', $code);
                if ($max) {
                    $errorMessage .=
                        Mage::helper('simigiftvoucher')->__('You have %d time(s) remaining to re-enter Gift Card code.',
                            $max - count($codes));
                }
                Mage::getSingleton('core/session')->addError($errorMessage);
                $this->_redirect("simigiftvoucher/index/addredeem");
                return;
            } else {
                //Hai.Tran
                $conditions = $giftVoucher->getConditionsSerialized();
                if (!empty($conditions)) {
                    $conditions = unserialize($conditions);
                    if (is_array($conditions) && !empty($conditions)) {
                        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('credit_condition') && isset($conditions['conditions'])
                            && $conditions['conditions']
                        ) {
                            Mage::getSingleton('core/session')->addError(
                                Mage::helper('simigiftvoucher')->__('Gift code "%s" has usage conditions, you cannot redeem it to Gift Card credit', $code));
                            $this->_redirect("simigiftvoucher/index/addredeem");
                            return;
                        }
                    }
                }
                $actions = $giftVoucher->getActionsSerialized();
                if (!empty($actions)) {
                    $actions = unserialize($actions);
                    if (is_array($actions) && !empty($actions)) {
                        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('credit_condition') && isset($actions['conditions'])
                            && $actions['conditions']
                        ) {
                            Mage::getSingleton('core/session')->addError(
                                Mage::helper('simigiftvoucher')->__('Gift code "%s" has usage conditions, you cannot redeem it to Gift Card credit', $code));
                            $this->_redirect("simigiftvoucher/index/addredeem");
                            return;
                        }
                    }
                }
                //End Hai.Tran
                if (!Mage::helper('simigiftvoucher')->canUseCode($giftVoucher)) {
                    Mage::getSingleton('core/session')->addError(
                        $this->__('The gift code usage has exceeded the number of users allowed.'));
                    return $this->_redirect("simigiftvoucher/index/index");
                }
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                if ($giftVoucher->getBalance() == 0) {
                    Mage::getSingleton('core/session')->addError(
                        Mage::helper('simigiftvoucher')->__('%s - The current balance of this gift code is 0.', $code));
                    $this->_redirect("simigiftvoucher/index/addredeem");
                    return;
                }
                if ($giftVoucher->getStatus() != 2 && $giftVoucher->getStatus() != 4) {
                    Mage::getSingleton('core/session')->addError(
                        Mage::helper('simigiftvoucher')->__('Gift code "%s" is not avaliable', $code));
                    $this->_redirect("simigiftvoucher/index/addredeem");
                    return;
                } else {
                    $balance = $giftVoucher->getBalance();

                    $credit = Mage::getModel('simigiftvoucher/credit')->getCreditAccountLogin();
                    $creditCurrencyCode = $credit->getCurrency();
                    $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
                    if (!$creditCurrencyCode) {
                        $creditCurrencyCode = $baseCurrencyCode;
                        $credit->setCurrency($creditCurrencyCode);
                        $credit->setCustomerId($customer->getId());
                    }

                    $voucherCurrency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
                    $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
                    $creditCurrency = Mage::getModel('directory/currency')->load($creditCurrencyCode);

                    $amountTemp = $balance * $balance / $baseCurrency->convert($balance, $voucherCurrency);
                    $amount = $baseCurrency->convert($amountTemp, $creditCurrency);

                    $credit->setBalance($credit->getBalance() + $amount);

                    $credithistory = Mage::getModel('simigiftvoucher/credithistory')
                        ->setCustomerId($customer->getId())
                        ->setAction('Redeem')
                        ->setCurrencyBalance($credit->getBalance())
                        ->setGiftcardCode($giftVoucher->getGiftCode())
                        ->setBalanceChange($balance)
                        ->setCurrency($giftVoucher->getCurrency())
                        ->setCreatedDate(now());
                    $history = Mage::getModel('simigiftvoucher/history')->setData(array(
                        'order_increment_id' => '',
                        'giftvoucher_id' => $giftVoucher->getId(),
                        'created_at' => now(),
                        'action' => Simi_Simigiftvoucher_Model_Actions::ACTIONS_REDEEM,
                        'amount' => $balance,
                        'balance' => 0.0,
                        'currency' => $giftVoucher->getCurrency(),
                        'status' => $giftVoucher->getStatus(),
                        'order_amount' => '',
                        'comments' => Mage::helper('simigiftvoucher')->__('Redeem to Gift Card credit balance'),
                        'extra_content' => Mage::helper('simigiftvoucher')->__('Redeemed by %s', $customer->getName()),
                        'customer_id' => $customer->getId(),
                        'customer_email' => $customer->getEmail(),
                    ));

                    try {
                        $giftVoucher->setBalance(0)->setStatus(Simi_Simigiftvoucher_Model_Status::STATUS_USED)->save();
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect("simigiftvoucher/index/addredeem");
                        return;
                    }

                    try {
                        $credit->save();
                    } catch (Exception $e) {
                        $giftVoucher->setBalance($balance)
                            ->setStatus(Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE)
                            ->save();
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect("simigiftvoucher/index/addredeem");
                        return;
                    }
                    try {
                        $history->save();
                        $credithistory->save();
                        Mage::getSingleton('core/session')->addSuccess(
                            Mage::helper('simigiftvoucher')->__('Gift card "%s" was successfully redeemed', $code));
                        $this->_redirect("simigiftvoucher/index/index");
                        return;
                    } catch (Exception $e) {
                        $giftVoucher->setBalance($balance)
                            ->setStatus(Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE)
                            ->save();
                        $credit->setBalance($credit->getBalance() - $amount)->save();
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect("simigiftvoucher/index/addredeem");
                        return;
                    }
                }
            }
        }

        $this->_redirect("simigiftvoucher/index/index");
    }

    /**
     * Get Gift code information
     *
     * @param array $giftvouchercode
     * @return Simi_Simigiftvoucher_Model_Giftvoucher_Collection
     */
    public function getGiftVoucher($giftvouchercode)
    {
        if ($giftvouchercode) {
            $codes = Mage::getSingleton('simigiftvoucher/session')->getCodes();
            $codes[] = $giftvouchercode;
            $codes = array_unique($codes);
            if ($max = Mage::helper('simigiftvoucher')->getGeneralConfig('maximum')) {
                if (count($codes) > $max) {
                    return null;
                }
            }

            Mage::getSingleton('simigiftvoucher/session')->setCodes($codes);
            $giftVoucher = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($giftvouchercode);
            if ($giftVoucher->getId()) {
                return $giftVoucher;
            }
        }
        return Mage::getModel('simigiftvoucher/giftvoucher');
    }

    /**
     * View gift code detail
     */
    public function viewAction()
    {
        $linked = Mage::getModel('simigiftvoucher/customervoucher')->load($this->getRequest()->getParam('id'));
        if ($linked->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
            return $this->_redirect('*/*/index');
        }
        $this->loadLayout();
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('simigiftvoucher/index/index');
        }
        $this->renderLayout();
    }

    /**
     * Print Gift Code
     */
    public function printAction()
    {

        $linked = Mage::getModel('simigiftvoucher/customervoucher')->load($this->getRequest()->getParam('id'));
        if ($linked->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
            return $this->_redirect('*/*/index');
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Print Gift Code email
     */
    public function printemailAction()
    {
        if ($key = $this->getRequest()->getParam('k')) {
            $keyDecode = explode('$', base64_decode($key));
            $simigiftvoucher = Mage::getModel('simigiftvoucher/giftvoucher')->load($keyDecode[1]);
            if ($simigiftvoucher && $simigiftvoucher->getId() && $simigiftvoucher->getGiftCode() == $keyDecode[0]) {
                Mage::app()->getRequest()->setParam('id', $simigiftvoucher->getId());
                $this->loadLayout();
                $this->renderLayout();
                return;
            }
        }
        return $this->_redirect('*/*/index');
    }

    /**
     * Email gift card to friend
     */
    public function emailAction()
    {
        $linked = Mage::getModel('simigiftvoucher/customervoucher')->load($this->getRequest()->getParam('id'));
        if ($linked->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
            return $this->_redirect('*/*/index');
        }
        $this->loadLayout();
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('simigiftvoucher/index/index');
        }
        $this->renderLayout();
    }

    /**
     * Send email to friend
     */
    public function sendEmailAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = $data['giftcard_id'];
            $giftCard = Mage::getModel('simigiftvoucher/giftvoucher')->load($id);

            if ($giftCard->getSetId() > 0 && $giftCard->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_PENDING) {
                Mage::getSingleton('core/session')->addError($this->__('Can not send email because it is Gift Code Set!'));
                return $this->_redirect('*/*/');
            }

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (!$customer ||
                ($giftCard->getCustomerId() != $customer->getId()
                    && $giftCard->getCustomerEmail() != $customer->getEmail()
                )
            ) {
                Mage::getSingleton('core/session')->addError($this->__('The Gift Card email has been failed to send.'));
                return $this->_redirect('*/*/');
            }

            $giftCard->addData($data);
            $giftCard->setNotResave(true);

            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            if ($giftCard->sendEmailToRecipient()) {
                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('The Gift Card email has been sent successfully.'));
            } else {
                Mage::getSingleton('core/session')->addError(
                    $this->__('The Gift Card email cannot be sent to your friend!'));
            }
            $translate->setTranslateInline(true);
        }
        $this->_redirect('*/*/');
    }

    /**
     * View balance history
     */
    public function historyAction()
    {
        $this->loadLayout();
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('simigiftvoucher/index/index');
        }
        $this->renderLayout();
    }

    /**
     * Upload custom images
     */
    public function customUploadAction()
    {
        try {
            if (Mage::getSingleton('customer/session')->getGiftcardCustomUploadImage()) {
                Mage::helper('simigiftvoucher')
                    ->deleteImageFile(Mage::getSingleton('customer/session')
                        ->getGiftcardCustomUploadImage());
            }
            $uploader = new Mage_Core_Model_File_Uploader('image');
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            Mage::helper('simigiftvoucher')->createImageFolderHaitv('', '', true);
            $result = $uploader->save(Mage::getBaseDir('media') . DS . 'tmp' . DS . 'simigiftvoucher' . DS . 'images' . DS);
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path'] = str_replace(DS, "/", $result['path']);
            $result['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
                'tmp/simigiftvoucher/images/' . $result['file'];
            Mage::getSingleton('customer/session')->setGiftcardCustomUploadImage($result['url']);
            Mage::getSingleton('customer/session')->setGiftcardCustomUploadImageName($result['file']);
            Mage::helper('simigiftvoucher')->resizeImage($result['url']);
        } catch (Exception $e) {
            $result = array('error' => $e->getMessage(), 'errorcode' => $e->getCode());
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Upload images
     */
    public function uploadImageAjaxAction()
    {
        $result = array();
        if (isset($_FILES['templateimage'])) {
            $error = $_FILES["templateimage"]["error"];

            try {
                $uploader = new Varien_File_Uploader('templateimage');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                Mage::helper('simigiftvoucher')->createImageFolderHaitv('', '', true);
                $fileName = $_FILES['templateimage']['name'];
                $result = $uploader->save(Mage::getBaseDir('media') . DS . 'tmp' . DS . 'simigiftvoucher' . DS .
                    'images' . DS, $fileName);
                $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
                $result['path'] = str_replace(DS, "/", $result['path']);
                $result['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
                    'tmp/simigiftvoucher/images/' . $result['file'];

                $result['filename'] = $fileName;
                $result['sucess'] = true;
            } catch (Exception $e) {
                $result['sucess'] = false;
                $result = array('error' => $e->getMessage(), 'errorcode' => $e->getCode());
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Image Saving Error!'));
            $result['sucess'] = false;
            $result = array('error' => $this->__('Image Saving Error!'));
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function testAction()
    {
        $data = array(
            'id' => 333
        );
        Mage::helper('simigiftvoucher/pdf')->printPdf('simigiftvoucher/print', 'simigiftvoucher/test.phtml', $data);
    }

    public function test1Action()
    {
        $data = array(
            'id' => 333
        );
        $html = Mage::helper('simigiftvoucher/pdf')->getHtml('simigiftvoucher/print', 'simigiftvoucher/test.phtml', $data);
        $this->getResponse()->setBody($html);
    }

}
