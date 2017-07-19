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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Giftproduct_Edit
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Giftproduct_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Giftproduct_Edit constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'simigiftvoucher';
        $this->_controller = 'adminhtml_giftproduct';

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * @return string
     */
    public function getHeaderText() {
        return Mage::helper('simigiftvoucher')->__('Add New Gift Card Product');
    }

}