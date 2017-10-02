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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Giftproduct_Edit_Tabs
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Giftproduct_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Giftproduct_Edit_Tabs constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('giftproduct_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simigiftvoucher')->__('Product Information'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('simigiftvoucher')->__('Settings'),
            'title' => Mage::helper('simigiftvoucher')->__('Settings'),
            'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_giftproduct_edit_tab_form')->toHtml(),
        ));


        return parent::_beforeToHtml();
    }

}