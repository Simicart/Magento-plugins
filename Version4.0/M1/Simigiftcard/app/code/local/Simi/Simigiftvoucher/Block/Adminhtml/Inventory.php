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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Inventory
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory {

    /**
     * @return bool
     * @throws Exception
     */
    public function isNew() {
        if ($this->getRequest()->getParam('type') == 'simigiftvoucher' && Mage::app()->getRequest()->getActionName() == 'new') {
            return false;
        }
        if ($this->getProduct()->getId()) {
            return false;
        }
        return true;
    }

}