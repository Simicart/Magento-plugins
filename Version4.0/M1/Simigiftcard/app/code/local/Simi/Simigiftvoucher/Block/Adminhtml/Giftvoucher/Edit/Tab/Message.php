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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Message
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Message extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
        protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('customer_fieldset', array('legend' => Mage::helper('simigiftvoucher')->__('Customer')));

        $fieldset->addField('customer_name', 'text', array(
            'label' => Mage::helper('simigiftvoucher')->__('Customer Name'),
            'required' => false,
            'name' => 'customer_name',
        ));

        $fieldset->addField('customer_email', 'text', array(
            'label' => Mage::helper('simigiftvoucher')->__('Customer Email'),
            'required' => false,
            'name' => 'customer_email',
        ));

        $fieldset = $form->addFieldset('recipient_fieldset', array('legend' => Mage::helper('simigiftvoucher')->__('Recipient')));

        $fieldset->addField('recipient_name', 'text', array(
            'label' => Mage::helper('simigiftvoucher')->__('Recipient Name'),
            'required' => false,
            'name' => 'recipient_name',
        ));

        $fieldset->addField('recipient_email', 'text', array(
            'label' => Mage::helper('simigiftvoucher')->__('Recipient Email'),
            'required' => false,
            'name' => 'recipient_email',
        ));

         $fieldset = $form->addFieldset('shipping_address', array('legend' => Mage::helper('simigiftvoucher')->__('Shipping Address')));

        $fieldset->addField('recipient_address', 'editor', array(
            'label' => Mage::helper('simigiftvoucher')->__('Recipient Address'),
            'name' => 'recipient_address',
            'style' => 'height:75px;',
        ));

        $fieldset = $form->addFieldset('message_fieldset', array('legend' => Mage::helper('simigiftvoucher')->__('Message')));

        $fieldset->addField('message', 'editor', array(
            'label' => Mage::helper('simigiftvoucher')->__('Message'),
            'required' => false,
            'name' => 'message',
        ));

        if (Mage::registry('giftvoucher_data')) {
            $form->addValues(Mage::registry('giftvoucher_data')->getData());
        }

        return parent::_prepareForm();
    }

}