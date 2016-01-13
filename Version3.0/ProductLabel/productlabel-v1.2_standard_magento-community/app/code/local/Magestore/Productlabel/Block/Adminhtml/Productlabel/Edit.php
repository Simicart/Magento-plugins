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
 * Productlabel Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
 */
class Magestore_Productlabel_Block_Adminhtml_Productlabel_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    protected function _prepareLayout() {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addItem('js', 'prototype/window.js')
                    ->addItem('js', 'mage/adminhtml/variables.js');
        }
        return parent::_prepareLayout();
    }

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'productlabel';
        $this->_controller = 'adminhtml_productlabel';
        $this->_updateButton('save', 'label', Mage::helper('productlabel')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('productlabel')->__('Delete'));
        $this->_addButton('save_apply', array(
            'class' => 'save',
            'label' => Mage::helper('productlabel')->__('Save and Apply'),
            'onclick' => "$('rule_auto_apply').value=1; editForm.submit()",
        ));
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            var templateControl = {
						variables: null,
						openVariableChooser: function() {
						Variables.init('text');
						if (this.variables == null) {
						Variables.resetData();
						this.variables = $('variables').value.evalJSON();
						}
						if (this.variables) {
							Variables.openVariableChooser(this.variables);
						}
						},
						openCategoryVariableChooser: function() {
						Variables.init('category_text');
						if (this.variables == null) {
						Variables.resetData();
						this.variables = $('variables').value.evalJSON();
						}
						if (this.variables) {
							Variables.openVariableChooser(this.variables);
						}
						}				
					};
            if($('rule_condition_selected').value!='custom')
            {
            $$('#productlabel_tabs_condition_content .rule-tree').each(function(el){el.hide();});
           
            }
            if($('rule_condition_selected').value!='onsale')
            {
                $('rule_threshold').disabled=true;
                $$('#rule_threshold')[0].up('tr').hide();
            }
            function modifyTargetElement(checkboxElem){
               
                if(checkboxElem.value=='custom'){
                   $$('#productlabel_tabs_condition_content .rule-tree').each(function(el){el.show();});
                }
                else{
                     $$('#productlabel_tabs_condition_content .rule-tree').each(function(el){el.hide();});
                }
                if(checkboxElem.value=='onsale'){

                     $$('#rule_threshold')[0].up('tr').show();
                }
                else{

                     $$('#rule_threshold')[0].up('tr').hide();
                }
                if(checkboxElem.value!='onsale'){
                    $('rule_threshold').disabled=true;
                }
                else $('rule_threshold').disabled=false;
            }
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productlabel_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'productlabel_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'productlabel_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            if($('is_auto_fill').value==1){
						
						$('insert_variable_category').parentElement.parentElement.hide();
						$('category_display').parentElement.parentElement.hide();
                                                $('category_image').parentElement.parentElement.hide();
						$('category_text').parentElement.parentElement.hide();
						$('category_position').parentElement.parentElement.hide();
//                                                $('category_display').disabled=false;
//                                                $('category_image').disabled=false;
//						$('category_text').disabled=false;
//						$('category_position').disabled=false;
                                                
						}
            function hidesetting(checkboxElem){
					if(checkboxElem.value==1){
						
						$('insert_variable_category').parentElement.parentElement.hide();
						$('category_display').parentElement.parentElement.hide();
                                                $('category_image').parentElement.parentElement.hide();
						$('category_text').parentElement.parentElement.hide();
						$('category_position').parentElement.parentElement.hide();
                                                $('category_text').parentElement.parentElement.class='';
                                                $('category_position').parentElement.parentElement.hide();

						}
					else {
						
						$('insert_variable_category').parentElement.parentElement.show();
						$('category_display').parentElement.parentElement.show();
                                                $('category_image').parentElement.parentElement.show();
						$('category_text').parentElement.parentElement.show();
						$('category_position').parentElement.parentElement.show();

                                                
						}
				  }
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('productlabel_data') && Mage::registry('productlabel_data')->getId()
        ) {
            return Mage::helper('productlabel')->__("Edit Label '%s'", $this->htmlEscape(Mage::registry('productlabel_data')->getName())
            );
        }
        return Mage::helper('productlabel')->__('Add Label');
    }

}