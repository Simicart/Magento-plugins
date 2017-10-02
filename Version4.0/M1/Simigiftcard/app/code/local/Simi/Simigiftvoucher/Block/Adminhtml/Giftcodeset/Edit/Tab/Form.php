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
 * Adminhtml Giftvoucher Generategiftcard Edit Tab Form Block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */

class Simi_Simigiftvoucher_Block_Adminhtml_Giftcodeset_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);



        $fieldset = $form->addFieldset('profile_fieldset', array('legend' => Mage::helper('simigiftvoucher')->__('Import Form')));

        if (isset($model) && $model->getId()) {
            $fieldset->addField('set_id', 'hidden', array('name' => 'set_id'));
        }

        if (Mage::getSingleton('adminhtml/session')->getGiftCodesetData()) {
            $data = Mage::getSingleton('adminhtml/session')->getGiftCodesetData();
            Mage::getSingleton('adminhtml/session')->setGiftcodesetData(null);
        } elseif (Mage::registry('giftcodeset_data')) {
            $data = Mage::registry('giftcodeset_data')->getData();
        }
        $disabled = false;


        $fieldset->addField('set_name', 'text', array(
            'label' => Mage::helper('simigiftvoucher')->__('Set Name '),
            'required' => true,
            'name' => 'set_name',
            'disabled' => $disabled,
        ));


        $fieldset->addField('import_code','file',array(
            'label' => Mage::helper('simigiftvoucher')->__('Import Gift Code Sets'),
            'name' => 'import_code',
            'required' => true,

        ));
        $notes=  Mage::helper('simigiftvoucher')->__('Status of Used : 1-Yes,2-No');
        $fieldset->addField('sample', 'note', array(
            'label' => Mage::helper('simigiftvoucher')->__('Download Sample CSV File'),
            'note' =>$notes,
            'text' => '<a href="' .
                $this->getUrl('*/*/downloadSampleSets') .
                '" title="' .
                Mage::helper('simigiftvoucher')->__('Download Sample Gift Code Set CSV File') .
                '">import_giftcodesets_sample.csv</a>'
        ));

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }



}
