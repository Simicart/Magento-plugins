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
 * Giftvoucher History Model
 * 
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @author      Simi Developer
 */

class Simi_Simigiftvoucher_Model_History extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('simigiftvoucher/history');
    }

    /**
     * Filter Gift Card history
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftVoucher
     * @param Mage_Sales_Model_Order $order
     * @param int $action
     * @return Simi_Simigiftvoucher_Model_History
     */
    public function getCollectionByOrderAction($giftVoucher, $order, $action)
    {
        return $this->getCollection()
                ->addFieldToFilter('giftvoucher_id', $giftVoucher->getId())
                ->addFieldToFilter('action', $action)
                ->addFieldToFilter('order_increment_id', $order->getIncrementId());
    }

    /**
     * Get the total amount of Gift Card spent in order
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftVoucher
     * @param Mage_Sales_Model_Order $order
     * @return float
     */
    public function getTotalSpent($giftVoucher, $order)
    {
        $total = 0;
        foreach ($this->getCollectionByOrderAction($giftVoucher, $order, 
            Simi_Simigiftvoucher_Model_Actions::ACTIONS_SPEND_ORDER) as $history) {
            $total += $history->getAmount();
        }
        return $total;
    }

    /**
     * Get the total amount of Gift Card refunded in order
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftVoucher
     * @param Mage_Sales_Model_Order $order
     * @return float
     */
    public function getTotalRefund($giftVoucher, $order)
    {
        $total = 0;
        foreach ($this->getCollectionByOrderAction($giftVoucher, $order, 
            Simi_Simigiftvoucher_Model_Actions::ACTIONS_REFUND) as $history) {
            $total += $history->getAmount();
        }
        return $total;
    }

}
