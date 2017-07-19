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
 * Adminhtml Giftvoucher Generategiftcard Edit Block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */

class Simi_Simigiftvoucher_Block_Adminhtml_Giftcodeset_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Giftcodeset_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'simigiftvoucher';
        $this->_controller = 'adminhtml_giftcodeset';

        $this->_updateButton('save', 'label', Mage::helper('simigiftvoucher')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('simigiftvoucher')->__('Delete'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "            
            function saveAndContinueEdit(){
                editForm.submit('" . $this->getUrl('*/*/save', array(
                'id' => $this->getRequest()->getParam('id'),
                'back' => 'edit'
            )) . "');
            }       
        ";
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('giftcodeset_data') && Mage::registry('giftcodeset_data')->getId()) {
            return Mage::helper('simigiftvoucher')->__("Edit Gift Code Set '%s'",
                $this->escapeHtml(Mage::registry('giftcodeset_data')->getSetName()));
        } else {
            return Mage::helper('simigiftvoucher')->__('New Gift Code Set');
        }
    }
}
