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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Product_Tab_Conditions
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Product_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $product = Mage::registry('current_product');
        $model = Mage::getSingleton('simigiftvoucher/product');
        if (!$model->getId() && $product->getId()) {
            $model->loadByProduct($product);
        }
        $data = $model->getData();
        $model->setData('conditions', $model->getData('conditions_serialized'));

        $configSettings = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
                array(
                    'add_widgets' => false,
                    'add_variables' => false,
                    'add_images' => false,
                    'files_browser_window_url' => $this->getBaseUrl() . 'admin/cms_wysiwyg_images/index/',
        ));

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('giftvoucher_');
        $fieldset = $form->addFieldset('description_fieldset', array('legend' => Mage::helper('simigiftvoucher')->__('Description')));

        $fieldset->addField('simigiftcard_description', 'editor', array(
            'label' => Mage::helper('simigiftvoucher')->__('Describe conditions applied to shopping cart when using this gift code'),
            'title' => Mage::helper('simigiftvoucher')->__('Describe conditions applied to shopping cart when using this gift code'),
            'name' => 'simigiftcard_description',
            'wysiwyg' => true,
            'config' => $configSettings,
        ));
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/giftvoucher_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array('legend' => Mage::helper('simigiftvoucher')->__('Allow using Gift Card only if the following shopping cart conditions are met (leave blank for all shopping carts)')))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('simigiftvoucher')->__('Conditions'),
            'title' => Mage::helper('simigiftvoucher')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
        $fieldset->addField('hidden', 'hidden', array(
            'name' => 'hidden',
            'after_element_html' => '
				  <script type="text/javascript">
                                  //Add validate data
                                  $("simigift_value").className+=" validate-number validate-greater-than-zero";
                                  $("simigift_from").className+=" validate-greater-than-zero validate-number validate-gift-range";
                                  $("simigift_to").className+=" validate-greater-than-zero validate-zero-or-greater ";
                                  $("simigift_dropdown").className+=" validate-greater-than-zero validate-gift-dropdown ";
                                  $("simigift_price").className+=" validate-gift-dropdown-price ";
                                  Event.observe(window, "load", function(){hidesettingGC();});
                                  if ($("simigift_type")) {
                                    Event.observe($("simigift_type"), "change", function()
                                        {
                                            hidesettingGC();
                                        }
                                        );
                                        }
                                    if ($("simigift_price_type")) {
                                    Event.observe($("simigift_price_type"), "change", function()
                                        {
                                            hidesettingGC();
                                        }
                                        );
                                        }    
//                                  $("simigift_type").on("change", function(event) {
//                                    
//                                  });
//                                   $("simigift_price_type").on("change", function(event) {
//                                    hidesettingGC();
//                                  });
				  function hidesettingGC(){
                                        if($("simigift_price_type").value==' . Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT . ')
                                        {
                                           $("simigift_price").disabled=true;
                                           $("simigift_price_type").up("td").down(".note").hide();
//                                           $("simigift_price_type").up("td").down(".note").update("' . $this->__('Gift Card price is the same as Gift Card value by default.') . '");
                                        }
                                        else if($("simigift_price_type").value==' . Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_FIX . ')
                                        {
                                           $("simigift_price").up("tr").down("label").update("' . $this->__("Gift Card price") . '<span class=\"required\">*</span>");
                                           $("simigift_price").disabled=false;
                                           $("simigift_price_type").up("td").down(".note").hide();
                                           $("simigift_price").up("td").down(".note").update("' . $this->__("Enter fixed price(s) corresponding to Gift Card value(s).For example:<br />Type of Gift Card value: Dropdown values<br />Gift Card values : 20,30,40<br />Gift Card price: 15,25,35<br />So customers only have to pay $25 for a $30 Gift card.") . '");
                                        }
                                        else if($("simigift_price_type").value==' . Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_PERCENT . ')
                                            {
                                                 $("simigift_price").up("tr").down("label").update("' . $this->__("Percentage") . '<span class=\"required\">*</span>");
                                                 $("simigift_price").disabled=false;
                                                 $("simigift_price_type").up("td").down(".note").hide();
                                                 $("simigift_price").up("td").down(".note").update("' . $this->__("Enter percentage(s) of Gift Card value(s) to calculate Gift Card price(s). For example:<br />Type of Gift Card value: Dropdown values<br />Gift Card values: 20,30,40<br />Percentage: 90,90,90<br />So customers only have to pay 90% of Gift Card value, $36 for a $40 Gift card for instance.") . '");
                                            }
					if($("simigift_type").value == ' . Simi_Simigiftvoucher_Model_Gifttype::GIFT_TYPE_FIX . '){
						$("simigift_value").disabled=false;
                                                $("simigift_from").disabled=true;
                                                $("simigift_to").disabled=true;
                                                $("simigift_dropdown").disabled=true;
                                                $("simigift_value").up("tr").show();
						$("simigift_from").up("tr").hide();
						$("simigift_to").up("tr").hide();
                                                $("simigift_dropdown").up("tr").hide();
                                                $("simigift_price_type")[1].show();
						}
					else if($("simigift_type").value == ' . Simi_Simigiftvoucher_Model_Gifttype::GIFT_TYPE_RANGE . '){
						$("simigift_value").disabled=true;
                                                 $("simigift_from").disabled=false;
                                                $("simigift_to").disabled=false;
                                                $("simigift_dropdown").disabled=true;
                                                $("simigift_value").up("tr").hide();
						$("simigift_from").up("tr").show();
						$("simigift_to").up("tr").show();
                                                $("simigift_price_type")[1].hide();
                                                $("simigift_dropdown").up("tr").hide();
                                                if($("simigift_price_type").value=="1")
                                                $("simigift_price_type")[0].selected=true;
                                                if($("simigift_price_type").value=="2")
                                                $("simigift_price_type")[2].selected=true;
                                                
						}
                                      else if($("simigift_type").value == ' . Simi_Simigiftvoucher_Model_Gifttype::GIFT_TYPE_DROPDOWN . '){
						$("simigift_value").disabled=true;
                                                $("simigift_from").disabled=true;
                                                $("simigift_to").disabled=true;
                                                $("simigift_dropdown").disabled=false;
                                                $("simigift_value").up("tr").hide();
						$("simigift_from").up("tr").hide();
						$("simigift_to").up("tr").hide();
                                                $("simigift_dropdown").up("tr").show();
                                                $("simigift_price_type")[1].show();
						}
                                     if($("simigift_price_type").value==' . Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT . '){
                                         $("simigift_price").up("tr").hide();
                                     }
                                     else $("simigift_price").up("tr").show();
				  }
                                error_range ="' . Mage::helper("simigiftvoucher")->__("Minimum Gift Card value must be lower than maximum Gift Card value.") . '";
                                Validation.add("validate-gift-range", error_range, function(v) {
                                   if(parseInt($("simigift_from").value)>parseInt($("simigift_to").value))
                                   return false;
                                   else return true;
                                });
                                error_dropdown ="' . Mage::helper("simigiftvoucher")->__("Input not correct") . '";
                                Validation.add("validate-gift-dropdown", error_dropdown, function(v) {
                                   parten=/^(\d,{0,1})+$/;
                                   
                                   return (parten.test($("simigift_dropdown").value));
                                });
                                Validation.add("validate-gift-dropdown-price", error_dropdown, function(v) {
                                   if($("simigift_dropdown").value && $("simigift_type").value == ' . Simi_Simigiftvoucher_Model_Gifttype::GIFT_TYPE_DROPDOWN . ')
                                   {
                                        cnt_dropdown=$("simigift_dropdown").value.split(",").length-1;
                                        if($("simigift_price").value)
                                        {
                                            cnt_giftprice=$("simigift_price").value.split(",").length-1;
                                            if(cnt_dropdown!==cnt_giftprice)
                                            {
                                            return false;
                                            }
                                            else return true;
                                        }
                                        
                                   }
                                   
                                   else return true;
                                });
				  </script>',
        ));

        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    public function getTabLabel() {
        return Mage::helper('simigiftvoucher')->__('Shopping Cart Conditions ');
    }

    /**
     * @return string
     */
    public function getTabTitle() {
        return Mage::helper('simigiftvoucher')->__('Shopping Cart Conditions ');
    }

    /**
     * @return bool
     */
    public function canShowTab() {
        if (Mage::registry('current_product')->getTypeId() == 'simigiftvoucher') {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden() {
        if (Mage::registry('current_product')->getTypeId() == 'simigiftvoucher') {
            return false;
        }
        return true;
    }

}
