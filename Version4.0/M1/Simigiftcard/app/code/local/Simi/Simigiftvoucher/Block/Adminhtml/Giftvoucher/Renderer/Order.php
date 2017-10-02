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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Renderer_Order
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($row->getOrderIncrementId());
        return sprintf('<a href="%s" title="%s">%s</a>', $this->getUrl('adminhtml/sales_order/view', array('order_id' => $order->getId())), Mage::helper('simigiftvoucher')->__('View Order Detail'), $row->getOrderIncrementId()
        );
    }

}
