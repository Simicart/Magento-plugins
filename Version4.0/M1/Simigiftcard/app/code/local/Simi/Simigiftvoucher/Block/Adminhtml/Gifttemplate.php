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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate constructor.
     */
        public function __construct() {
        $this->_controller = 'adminhtml_gifttemplate';
        $this->_blockGroup = 'simigiftvoucher';
        $this->_headerText = Mage::helper('simigiftvoucher')->__('Gift Card Template Manager');
        $this->_addButtonLabel = Mage::helper('simigiftvoucher')->__('Add Gift Card Template');
        parent::__construct();
    }

}