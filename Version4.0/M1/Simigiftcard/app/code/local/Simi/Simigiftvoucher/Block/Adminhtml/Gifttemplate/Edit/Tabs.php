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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Edit_Tabs
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Edit_Tabs constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('giftvoucher_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simigiftvoucher')->__('Gift Card Template Information'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml() {
        
        if (!Mage::helper('simigiftvoucher')->isShowOnlySimpleTemplate()) {
            $this->addTab('form_section', array(
                'label' => Mage::helper('simigiftvoucher')->__('General Information'),
                'title' => Mage::helper('simigiftvoucher')->__('General Information'),
                'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_gifttemplate_edit_tab_form')->toHtml(),
            ));
        } else {
            $this->addTab('new_form_section', array(
                'label' => Mage::helper('simigiftvoucher')->__('General Information'),
                'title' => Mage::helper('simigiftvoucher')->__('General Information'),
                'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_gifttemplate_edit_tab_newform')->toHtml(),
            ));
        }

        $this->addTab('images_section', array(
            'label' => Mage::helper('simigiftvoucher')->__('Images'),
            'title' => Mage::helper('simigiftvoucher')->__('Images'),
            'content' => $this->getLayout()->createBlock('simigiftvoucher/adminhtml_gifttemplate_edit_tab_images')->toHtml(),
        ));


        return parent::_beforeToHtml();
    }

}
