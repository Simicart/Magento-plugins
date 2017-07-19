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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Import_Form
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Import_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/processImport'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('profile_fieldset', array('legend' => Mage::helper('simigiftvoucher')->__('Import Form')));

        $fieldset->addField('filecsv', 'file', array(
            'label' => Mage::helper('simigiftvoucher')->__('Import File'),
            'title' => Mage::helper('simigiftvoucher')->__('Import File'),
            'name' => 'filecsv',
            'required' => true,
        ));

        $fieldset->addField('sample', 'note', array(
            'label' => Mage::helper('simigiftvoucher')->__('Download Sample CSV File'),
            'text' => '<a href="' .
            $this->getUrl('*/*/downloadSample') .
            '" title="' .
            Mage::helper('simigiftvoucher')->__('Download Sample CSV File') .
            '">import_giftcode_sample.csv</a>'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}