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
 * Class Simi_Simigiftvoucher_Model_Total_Pdf_Giftcardcredit
 */
class Simi_Simigiftvoucher_Model_Total_Pdf_Giftcardcredit extends Mage_Sales_Model_Order_Pdf_Total_Default
{
	/**
	 * @return array
     */
	public function getTotalsForDisplay(){
		$amount = $this->getAmount();
		$fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
		if(floatval($amount)){
			$amount = $this->getOrder()->formatPriceTxt($amount);
			if ($this->getAmountPrefix()){
				$discount = $this->getAmountPrefix().$discount;
			}
			$totals = array(array(
				'label' => Mage::helper('simigiftvoucher')->__('Gift Card credit'),
				'amount' => $amount,
				'font_size' => $fontSize,
				)
			);	
			return $totals;
		}
	}

	/**
	 * @return mixed
     */
	public function getAmount(){
        if ($this->getSource()->getSimiuseGiftCreditAmount()) {
            return -$this->getSource()->getSimiuseGiftCreditAmount();
        }
		return -$this->getOrder()->getSimiuseGiftCreditAmount();
	}
}
