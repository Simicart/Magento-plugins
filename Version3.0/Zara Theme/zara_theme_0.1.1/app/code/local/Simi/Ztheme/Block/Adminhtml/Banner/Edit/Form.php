<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Ztheme
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Ztheme Edit Form Tab Block
 * 
 * @category    
 * @package     Ztheme
 * @author      Developer
 */
class Simi_Ztheme_Block_Adminhtml_Banner_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare form's information for block
     *
     * @return Simi_Ztheme_Block_Adminhtml_Ztheme_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array(
                'banner_id'    => $this->getRequest()->getParam('banner_id'),
            )),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}