<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Productlabel Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
 */
class Magestore_Productlabel_Block_Adminhtml_Productlabel_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

   

    public function __construct() {
        parent::__construct();
        $this->setId('productlabel_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('productlabel')->__('Manage Product Labels'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Productlabel_Block_Adminhtml_Productlabel_Edit_Tabs
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('productlabel')->__('General Information'),
            'title' => Mage::helper('productlabel')->__('General Information'),
            'content' => $this->getLayout()
                    ->createBlock('productlabel/adminhtml_productlabel_edit_tab_form')
                    ->toHtml(),
        ));
        $this->addTab('condition', array(
            'label' => Mage::helper('productlabel')->__('Conditions'),
            'title' => Mage::helper('productlabel')->__('Conditions'),
            'content' => $this->getLayout()->createBlock('productlabel/adminhtml_productlabel_edit_tab_conditions')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}