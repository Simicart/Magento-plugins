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
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Simi Edit Form Content Tab Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */

class Simi_Simivideo_Block_Adminhtml_Simivideo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);          
        if (Mage::getSingleton('adminhtml/session')->getConnectorData()) {
            $data = Mage::getSingleton('adminhtml/session')->getConnectorData();
            Mage::getSingleton('adminhtml/session')->setConnectorData(null);
        } elseif (Mage::registry('simivideo_data'))
            $data = Mage::registry('simivideo_data')->getData();

        $fieldset = $form->addFieldset('simivideo_form', array('legend' => Mage::helper('simivideo')->__('Video information')));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('simivideo')->__('Status'),
            'name' => 'status',
            'values' => Mage::getSingleton('simivideo/status')->getOptionHash(),
        ));
        
        $fieldset->addField('video_title', 'text', array(
            'label' => Mage::helper('simivideo')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'video_title',
        ));

        
        $fieldset->addField('video_url', 'text', array(
            'name' => 'video_url',
            'required' => true,
            'label' => Mage::helper('simivideo')->__('Youtube Video URL'),
            'note' => Mage::helper('simivideo')->__('Example: https://www.youtube.com/watch?v=AfgX7GB_Rkc'),
        ));
        /*
        $fieldset->addField('product_ids', 'editor', array(
            'label' => Mage::helper('simivideo')->__('Products'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'product_ids',
        ));
*/
        $productIds = implode(", ", Mage::getResourceModel('catalog/product_collection')->getAllIds());
        $fieldset->addField('product_ids', 'text', array(
            'name' => 'product_ids',
            'class' => 'required-entry',
            'required' => true,
            'label' => Mage::helper('simivideo')->__('Product ID'),
            'note'  => Mage::helper('simivideo')->__('Choose a product'),
            'after_element_html' => '<a id="product_link" href="javascript:void(0)" onclick="toggleMainProducts()"><img src="' . $this->getSkinUrl('images/rule_chooser_trigger.gif') . '" alt="" class="v-middle rule-chooser-trigger" title="Select Products"></a><input type="hidden" value="'.$productIds.'" id="product_all_ids"/><div id="main_products_select" style="display:none;width:640px"></div>
                <script type="text/javascript">
                    function toggleMainProducts(){
                        if($("main_products_select").style.display == "none"){
                            var url = "' . $this->getUrl('simivideoadmin/adminhtml_simivideo/chooserMainProducts', array('_secure'=>true)) . '";
                            var params = $("product_ids").value.split(", ");
                            var parameters = {"form_key": FORM_KEY,"selected[]":params };
                            var request = new Ajax.Request(url,
                            {
                                evalScripts: true,
                                parameters: parameters,
                                onComplete:function(transport){
                                    $("main_products_select").update(transport.responseText);
                                    $("main_products_select").style.display = "block"; 
                                }
                            });
                        }else{
                            $("main_products_select").style.display = "none";
                        }
                    };
                    var grid;
                   
                    function constructData(div){
                        grid = window[div.id+"JsObject"];
                        if(!grid.reloadParams){
                            grid.reloadParams = {};
                            grid.reloadParams["selected[]"] = $("product_ids").value.split(", ");
                        }
                    }
                    function toogleCheckAllProduct(el){
                        if(el.checked == true){
                            $$("#main_products_select input[type=checkbox][class=checkbox]").each(function(e){
                                if(e.name != "check_all"){
                                    if(!e.checked){
                                        if($("product_ids").value == "")
                                            $("product_ids").value = e.value;
                                        else
                                            $("product_ids").value = $("product_ids").value + ", "+e.value;
                                        e.checked = true;
                                        grid.reloadParams["selected[]"] = $("product_ids").value.split(", ");
                                    }
                                }
                            });
                        }else{
                            $$("#main_products_select input[type=checkbox][class=checkbox]").each(function(e){
                                if(e.name != "check_all"){
                                    if(e.checked){
                                        var vl = e.value;
                                        if($("product_ids").value.search(vl) == 0){
                                            if($("product_ids").value == vl) $("product_ids").value = "";
                                            $("product_ids").value = $("product_ids").value.replace(vl+", ","");
                                        }else{
                                            $("product_ids").value = $("product_ids").value.replace(", "+ vl,"");
                                        }
                                        e.checked = false;
                                        grid.reloadParams["selected[]"] = $("product_ids").value.split(", ");
                                    }
                                }
                            });
                            
                        }
                    }
                    function selectProduct(e) {
                        if(e.checked == true){
                            if(e.id == "main_on"){
                                $("product_ids").value = $("product_all_ids").value;
                            }else{
                                if($("product_ids").value == "")
                                    $("product_ids").value = e.value;
                                else
                                    $("product_ids").value = $("product_ids").value + ", "+e.value;
                                    
                                grid.reloadParams["selected[]"] = $("product_ids").value;
                            }
                        }else{
                             if(e.id == "main_on"){
                                $("product_ids").value = "";
                            }else{
                                var vl = e.value;
                                if($("product_ids").value.search(vl) == 0){
                                    $("product_ids").value = $("product_ids").value.replace(vl+", ","");
                                }else{
                                    $("product_ids").value = $("product_ids").value.replace(", "+ vl,"");
                                }
                            }
                        }
                        
                    }
                </script>'
        ));
        
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}