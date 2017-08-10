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
 * Adminhtml Giftvoucher Checkout controller
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Adminhtml_Simigiftvoucher_CheckoutController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Remove Gift code from Order
     */
    public function removegiftAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $code = trim($this->getRequest()->getParam('code'));
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

        if ($success) {
            $codes = implode(',', $codesArray);
            $session->setSimigiftCodes($codes);
            Mage::getSingleton('adminhtml/session_quote')->addSuccess(
                $this->__('Gift card "%s" has been removed successfully.', $code));
        } else {
            Mage::getSingleton('adminhtml/session_quote')->addError(
                $this->__('Gift card "%s" not found!', $code));
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array()));
    }

    /**
     * Add Gift code to Order
     */
    public function giftcardPostAction()
    {
        $request = $this->getRequest();
        $session = Mage::getSingleton('checkout/session');
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();

        if ($quote->getCouponCode() && !Mage::helper('simigiftvoucher')->getGeneralConfig('use_with_coupon')) {
            $this->clearGiftcardSession($session);
            Mage::getSingleton('adminhtml/session_quote')->addNotice(
                Mage::helper('simigiftvoucher')->__('A coupon code has been used. You cannot apply gift codes or Gift Card credit with the coupon to get discount.'));
        } else {
            if ($request->isPost()) {
                if (Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit', $quote->getStoreId())
                    && $request->getParam('giftvoucher_credit')
                ) {
                    $session->setSimiuseGiftCardCredit(1);
                    $session->setSimimaxCreditUsed(floatval($request->getParam('credit_amount')));
                } else {
                    $session->setSimiuseGiftCardCredit(0);
                    $session->setSimimaxCreditUsed(null);
                }
                if ($request->getParam('giftvoucher')) {
                    $session->setSimiuseGiftCard(1);
                    $giftcodesAmount = $request->getParam('giftcodes');
                    if (count($giftcodesAmount)) {
                        $giftMaxUseAmount = unserialize($session->getSimigiftMaxUseAmount());
                        if (!is_array($giftMaxUseAmount)) {
                            $giftMaxUseAmount = array();
                        }
                        $giftMaxUseAmount = array_merge($giftMaxUseAmount, $giftcodesAmount);
                        $session->setSimigiftMaxUseAmount(serialize($giftMaxUseAmount));
                    }
                    $addcodes = array();
                    if ($request->getParam('existed_giftvoucher_code')) {
                        $addcodes[] = trim($request->getParam('existed_giftvoucher_code'));
                    }
                    if ($request->getParam('giftvoucher_code')) {
                        $addcodes[] = trim($request->getParam('giftvoucher_code'));
                    }
                    if (count($addcodes)) {
                        foreach ($addcodes as $code) {
                            $giftVoucher = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
                            if (!$giftVoucher->getId()) {
                                Mage::getSingleton('adminhtml/session_quote')->addError(
                                    $this->__('Gift card "%s" is invalid.', $code));
                            } else {
                                $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
                                if ($giftVoucher->getBaseBalance() > 0
                                    && $giftVoucher->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE
                                    && $giftVoucher->validate($quote->setQuote($quote))
                                ) {
                                    $giftVoucher->addToSession($session);
                                    if ($giftVoucher->getCustomerId() == Mage::getSingleton('adminhtml/session_quote')
                                            ->getCustomerId() && $giftVoucher->getRecipientName()
                                        && $giftVoucher->getRecipientEmail() && $giftVoucher->getCustomerId()
                                    ) {
                                        Mage::getSingleton('adminhtml/session_quote')->addNotice(
                                            $this->__('Gift Card "%" has been sent to the customer\'s friend.', $code));
                                    }
                                    Mage::getSingleton('adminhtml/session_quote')->addSuccess(
                                        $this->__('Gift Card "%s" has been applied successfully.', $code));
                                } else {
                                    Mage::getSingleton('adminhtml/session_quote')->addError(
                                        $this->__('Gift Card "%s" is no longer available to use.', $code));
                                }
                            }
                        }
                    } else {
                        Mage::getSingleton('adminhtml/session_quote')->addSuccess(
                            $this->__('Gift Card has been updated successfully.'));
                    }
                } elseif ($session->getSimiuseGiftCard()) {
                    $session->setSimiuseGiftCard(null);
                    Mage::getSingleton('adminhtml/session_quote')->addSuccess(
                        $this->__('Your Gift Card has been removed successfully.'));
                }
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array()));
    }

    /**
     * Clear Gift Card session
     * @param $session
     */
    public function clearGiftcardSession($session)
    {
        if ($session->getSimiuseGiftCard()) {
            $session->setSimiuseGiftCard(null)
                ->setSimigiftCodes(null)
                ->setSimibaseAmountUsed(null)
                ->setSimibaseGiftVoucherDiscount(null)
                ->setSimigiftVoucherDiscount(null)
                ->setSimicodesBaseDiscount(null)
                ->setSimicodesDiscount(null)
                ->setSimigiftMaxUseAmount(null);
        }
        if ($session->getSimiuseGiftCardCredit()) {
            $session->setSimiuseGiftCardCredit(null)
                ->setSimimaxCreditUsed(null)
                ->setSimibaseUseGiftCreditAmount(null)
                ->setSimiuseGiftCreditAmount(null);
        }
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simigiftvoucher');
    }
}
