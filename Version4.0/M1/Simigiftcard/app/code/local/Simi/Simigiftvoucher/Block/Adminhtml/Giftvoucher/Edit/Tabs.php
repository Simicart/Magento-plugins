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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tabs
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tabs constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('giftvoucher_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simigiftvoucher')->__('Gift Code Information'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('simigiftvoucher')->__('General Information'),
            'title' => Mage::helper('simigiftvoucher')->__('General Information'),
            'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_giftvoucher_edit_tab_form')->toHtml(),
        ));

        $this->addTab('condition', array(
            'label' => Mage::helper('simigiftvoucher')->__('Shopping Cart Conditions'),
            'title' => Mage::helper('simigiftvoucher')->__('Shopping Cart Conditions'),
            'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_giftvoucher_edit_tab_conditions')->toHtml(),
        ));
        $this->addTab('action', array(
            'label' => Mage::helper('simigiftvoucher')->__('Cart Item Conditions'),
            'title' => Mage::helper('simigiftvoucher')->__('Cart Item Conditions'),
            'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_giftvoucher_edit_tab_actions')->toHtml(),
        ));
        $this->addTab('message_section', array(
            'label' => Mage::helper('simigiftvoucher')->__('Message Information'),
            'title' => Mage::helper('simigiftvoucher')->__('Message Information'),
            'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_giftvoucher_edit_tab_message')->toHtml(),
        ));

        if ($id = $this->getRequest()->getParam('id')) {
            if ($shipment = $this->getShipment($id)) {
                $this->addTab('shipping_and_tracking', array(
                    'label' => Mage::helper('simigiftvoucher')->__('Shipping and Tracking'),
                    'title' => Mage::helper('simigiftvoucher')->__('Shipping and Tracking'),
                    'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_giftvoucher_edit_tab_shipping')
                            ->setShipment($shipment)
                            ->toHtml(),
                ));
            }
            $this->addTab('history_section', array(
                'label' => Mage::helper('simigiftvoucher')->__('Transaction History'),
                'title' => Mage::helper('simigiftvoucher')->__('Transaction History'),
                'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_giftvoucher_edit_tab_history')->setGiftvoucher($id)->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

    /**
     * @param $giftCardId
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getShipment($giftCardId) {
        $history = Mage::getResourceModel('simigiftvoucher/history_collection')
                ->addFieldToFilter('giftvoucher_id', $giftCardId)
                ->addFieldToFilter('action', Simi_Simigiftvoucher_Model_Actions::ACTIONS_CREATE)
                ->getFirstItem();
        if (!$history->getOrderIncrementId() || !$history->getOrderItemId()) {
            return false;
        }
        $orderItem = Mage::getModel('sales/order_item')->load($history->getOrderItemId());
        $requestInfo = $orderItem->getProductOptionByCode('info_buyRequest');
        if (!isset($requestInfo['send_friend'])) {
            return false;
        }
        if (!$requestInfo['send_friend']) {
            return false;
        }
        $shipmentItem = Mage::getResourceModel('sales/order_shipment_item_collection')
                ->addFieldToFilter('order_item_id', $history->getOrderItemId())
                ->getFirstItem();
        if (!$shipmentItem || !$shipmentItem->getId()) {
            return true;
        }
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentItem->getParentId());
        if (!$shipment->getId()) {
            return true;
        }
        return $shipment;
    }

}
