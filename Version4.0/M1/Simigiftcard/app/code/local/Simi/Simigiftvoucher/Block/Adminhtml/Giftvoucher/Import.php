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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Import
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Import extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Import constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'simigiftvoucher';
        $this->_controller = 'adminhtml_giftvoucher';
        $this->_mode = 'import';
        $this->_updateButton('save', 'label', Mage::helper('simigiftvoucher')->__('Import'));
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_addButton('print', array(
            'label' => Mage::helper('simigiftvoucher')->__('Import and Print'),
            'onclick' => "importAndPrint()",
            'class' => 'save'
                ), 100);

        $this->_formScripts[] = "
            function importAndPrint(){
             
//             $('edit_form').target = '_blank';
                editForm.submit('" . $this->getUrl('*/*/processImport', array(
                    'print' => 'true',
                )) . "');
               
            }
        ";
    }

    /**
     * @return string
     */
    public function getHeaderText() {
        return Mage::helper('simigiftvoucher')->__('Import Gift Codes');
    }

}