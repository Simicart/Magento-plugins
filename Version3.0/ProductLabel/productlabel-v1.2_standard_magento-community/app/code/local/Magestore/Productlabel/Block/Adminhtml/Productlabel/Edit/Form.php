<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Productlabel
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Productlabel Edit Form Tab Block
 * 
 * @category    
 * @package     Productlabel
 * @author      Developer
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