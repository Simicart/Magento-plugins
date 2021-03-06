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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Edit
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Edit constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'simigiftvoucher';
        $this->_controller = 'adminhtml_gifttemplate';

        $this->_updateButton('save', 'label', Mage::helper('simigiftvoucher')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('simigiftvoucher')->__('Delete'));


		$this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        $this->_addButton('preview', array(
            'label' => Mage::helper('adminhtml')->__('Preview'),
            'onclick' => 'previewImage()',
            'class' => 'save',
                ), -100);



        $this->_formScripts[] = "
                function saveAndContinueEdit(){
                editForm.submit('" . $this->getUrl('*/*/save', array(
                    'id' => $this->getRequest()->getParam('id'),
                    'back' => 'edit'
                )) . "');
                    
            }
            
            function removeImage(element){
                
                new Ajax.Request('"
                . $this->getUrl('*/*/removeimage', array('_current' => true))
                . "', {
                            parameters: {
                                         form_key: FORM_KEY,
                                         value: element,
                                         
                                         },
                            evalScripts: true,
                            onSuccess: function(transport) {
                                if(transport.responseText=='success'){
                                 $(element).remove();
                                 if(!$('fileuploaded').down('img')) $('fileuploaded').up('tr').hide();
                                }
                            }
                        });
            }
            function previewImage(element){                
                edit_form=$('edit_form').serialize(true);
                form_data=Object.toJSON(edit_form);
                new Ajax.Request('"
                . $this->getUrl('*/*/previewimage', array('_current' => true))
                . "', {
                            method:'post',
                            parameters: {
                                
                                         form_key: FORM_KEY,
                                         value: element,
                                         form_data:form_data  
                                         },
                            evalScripts: true,
                            onSuccess: function(transport) {
                               TINY.box.show();
                                $('tinycontent').update(transport.responseText);
                            }
                        });
            }
            

            Event.observe(window, 'load', function(){changePattern();});
            function changePattern(){
				$('giftcard-notes-center').hide();
				$('giftcard-notes-top').hide();
				$('giftcard-notes-left').hide();
                $('giftcard-notes-simple').hide();
				$('gifttemplate_form').setStyle({height: 'auto'});
                $('demo_pattern').setStyle({top: 'inherit'});
                
                template_id=$('design_pattern').value;
                $('demo_pattern').down('img').src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA). '/simigiftvoucher/template/pattern/GC_'."'+template_id+'.jpg';
                if(template_id == ".Simi_Simigiftvoucher_Model_Designpattern::PATTERN_CENTER.")
				{
					$('background_img').up('tr').hide();
                    $('caption').up('tr').show();
                    $('notes').up('tr').show();
					$('giftcard-notes-center').show();
					$('note_style_color').down('span').innerHTML = '".Mage::helper('simigiftvoucher')->__('Choose color of texts in Gift Card title, value and gift code fields. (Recommended color: #FFFFFF)')."';
					$('note_text_color').down('span').innerHTML = '".Mage::helper('simigiftvoucher')->__('Choose color of other texts (fields title, notes, etc.). (Recommended color: #A9A7A7)')."';
				}
                else 
				{					
					if (template_id == ".Simi_Simigiftvoucher_Model_Designpattern::PATTERN_TOP."){
                        $('background_img').up('tr').show();
                        $('caption').up('tr').show();
                        $('notes').up('tr').show();
						$('note_background_img').down('span').innerHTML = '600x175. Support jpg, jpeg, gif, png files';
						$('giftcard-notes-top').show();
						$('note_style_color').down('span').innerHTML = '".Mage::helper('simigiftvoucher')->__('Choose color of texts in Gift Card title, value and gift code fields. (Recommended color: #FFFFFF)')."';
						$('note_text_color').down('span').innerHTML = '".Mage::helper('simigiftvoucher')->__('Choose color of other texts (fields title, notes, etc.). (Recommended color: #636363)')."';
					}	
					if (template_id == ".Simi_Simigiftvoucher_Model_Designpattern::PATTERN_LEFT."){
                        $('background_img').up('tr').show();
                        $('caption').up('tr').show();
                        $('notes').up('tr').show();
						$('note_background_img').down('span').innerHTML = '350x365. Support jpg, jpeg, gif, png files';
						$('giftcard-notes-left').show();
						$('note_style_color').down('span').innerHTML = '".Mage::helper('simigiftvoucher')->__('Choose color of texts in Gift Card title, value and gift code fields. (Recommended color: #DC8C71)')."';
						$('note_text_color').down('span').innerHTML = '".Mage::helper('simigiftvoucher')->__('Choose color of other texts (fields title, notes, etc.). (Recommended color: #949392)')."';	
					}
                    if (template_id == ".Simi_Simigiftvoucher_Model_Designpattern::PATTERN_SIMPLE." || template_id == ".Simi_Simigiftvoucher_Model_Designpattern::PATTERN_AMAZON."){
                        $('gifttemplate_form').setStyle({height: '580Px'});
                        $('demo_pattern').setStyle({top: '15px'});
                        $('background_img').up('tr').hide();
                        $('notes').up('tr').hide();
						$('giftcard-notes-simple').show();
                        $('caption').up('tr').hide();
                        $('caption').value = 'Gift Card';
						$('note_style_color').down('span').innerHTML = '".Mage::helper('simigiftvoucher')->__('Choose color of texts in gift code field. (Recommended color: #F05756)')."';
						$('note_text_color').down('span').innerHTML = '".Mage::helper('simigiftvoucher')->__('Choose color of texts in Gift Card message and value. (Recommended color: #464646)')."';
					}
				}	
            }
        ";
    }

    /**
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('gifttemplate_data') && Mage::registry('gifttemplate_data')->getId()) {
            return Mage::helper('simigiftvoucher')->__("Edit Gift Card Template '%s'", $this->htmlEscape(Mage::registry('gifttemplate_data')->getTemplateName()));
        } else {
            return Mage::helper('simigiftvoucher')->__('New Gift Card Template');
        }
    }

}