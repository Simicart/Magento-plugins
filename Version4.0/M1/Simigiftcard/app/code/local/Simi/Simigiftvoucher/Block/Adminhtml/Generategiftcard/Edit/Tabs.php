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
 * Adminhtml Giftvoucher Generategiftcard Edit Tabs Block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Generategiftcard_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Generategiftcard_Edit_Tabs constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftproduct_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simigiftvoucher')->__('Pattern Information'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('simigiftvoucher')->__('General Information'),
            'title' => Mage::helper('simigiftvoucher')->__('General Information'),
            'content' => $this->getLayout()
                ->createBlock('simigiftvoucher/adminhtml_generategiftcard_edit_tab_form')->toHtml(),
        ));
        $this->addTab('conditions_section', array(
            'label' => Mage::helper('simigiftvoucher')->__('Conditions'),
            'title' => Mage::helper('simigiftvoucher')->__('Conditions'),
            'content' => $this->getLayout()
                ->createBlock('simigiftvoucher/adminhtml_generategiftcard_edit_tab_conditions')->toHtml(),
        ));

        $isGenerated = $this->getTemplateGenerate()->getIsGenerated();
        if ($isGenerated) {
            $this->addTab('form_giftcode', array(
                'label' => Mage::helper('simigiftvoucher')->__('Gift Codes Information'),
                'title' => Mage::helper('simigiftvoucher')->__('Gift Codes Information'),
                'content' => $this->getLayout()
                    ->createBlock('simigiftvoucher/adminhtml_generategiftcard_edit_tab_giftcodelist')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return false|Mage_Core_Model_Abstract|mixed
     */
    public function getTemplateGenerate()
    {
        if (Mage::registry('template_data')) {
            return Mage::registry('template_data');
        }
        return Mage::getModel('simigiftvoucher/template');
    }

}
