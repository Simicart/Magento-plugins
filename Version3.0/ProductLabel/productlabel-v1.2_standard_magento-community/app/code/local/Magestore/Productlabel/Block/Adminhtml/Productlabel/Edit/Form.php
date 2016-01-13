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
 * @category     Magestore
 * @package     Magestore_Productlabel
 * @copyright     Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Productlabel Edit Form Block
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
 */
class Magestore_Productlabel_Block_Adminhtml_Productlabel_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare form's information for block
     *
     * @return Magestore_Productlabel_Block_Adminhtml_Productlabel_Edit_Form
     */
//    protected function _prepareLayout() {
//        if ($head = $this->getLayout()->getBlock('head')) {
//            $head->addItem('js', 'prototype/window.js')
//                    ->addItem('js', 'mage/adminhtml/variables.js');
//        }
//        return parent::_prepareLayout();
//    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array(
                'id' => $this->getRequest()->getParam('id'),
                'store' => $this->getRequest()->getParam('store')
            )),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
//        protected function _prepareLayout() {
//        if ($head = $this->getLayout()->getBlock('head')) {
//            $head->addItem('js', 'prototype/window.js')
//                    ->addItem('js', 'mage/adminhtml/variables.js');
//        }
//        return parent::_prepareLayout();
//    }

}