<?php

class Magestore_Productlabel_Block_Adminhtml_Catalog_Product_Productlabeltabs extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

	protected function _toHtml() {
	return '<ul class="messages"><li class="notice-msg"><ul><li><span>'.Mage::helper('productlabel')->__('The label created here will be the only one displayed on the product image. All of the labels created on the Manage Product Labels page have lower levels of priority.').'</span></li></ul></li></ul>' . parent::_toHtml();
	}

    /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel() {
        return $this->__('Product Label');
    }

    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->__('Product Label');
    }

    /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden() {
        return false;
    }

    protected function _prepareLayout() {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addItem('js', 'prototype/window.js')
                    ->addItem('js', 'mage/adminhtml/variables.js');
        }
        return parent::_prepareLayout();
    }

    // Form Edit Product Label
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $data['same_on_two_page'] = 0;
        $dataObj = new Varien_Object(array(
            'store_id' => '',
            'display_in_store',
            'position_in_store',
            'text_in_store',
            'category_display_in_store',
            'category_position_in_store',
            'category_text_in_store'
        ));
        if (Mage::getSingleton('adminhtml/session')->getProductlabelentityData()) {
            $data = Mage::getSingleton('adminhtml/session')->getProductlabelentityData();
            Mage::getSingleton('adminhtml/session')->setProductlabelentityData(null);
        } elseif (Mage::registry('productlabelentity_data')) {
            $data = Mage::registry('productlabelentity_data')->getData();
        }
        if (isset($data))
            $dataObj->addData($data);
        $data = $dataObj->getData();

        $storeId = $this->getRequest()->getParam('store');
        if ($storeId)
            $store = Mage::getModel('core/store')->load($storeId);
        else
            $store = Mage::app()->getStore();
        $data['store_id'] = $storeId;
        //		Product Page Label
        $fieldset2 = $form->addFieldset('productlabel_product_page', array(
            'legend' => Mage::helper('productlabel')->__('Label on Product Page')
			));
        $inStore = $this->getRequest()->getParam('store');
        $defaultLabel = Mage::helper('productlabel')->__('Use Default');
        $defaultTitle = Mage::helper('productlabel')->__('-- Please Select --');
        $scopeLabel = Mage::helper('productlabel')->__('STORE VIEW');
        $fieldset2->addField('display', 'select', array(
            'label' => Mage::helper('productlabel')->__('Display'),
            'name' => 'display',
            'required' => true,
            'values' => array(0 => 'No', 1 => 'Yes'),
            'disabled' => ($inStore && !$data['display_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="display_default" name="display_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['display_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="display_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
        ));
        $fieldset2->addField('store_id', 'hidden', array(
            'name' => 'store_id',
        ));

        $fieldset2->addField('position', 'select', array(
            'label' => Mage::helper('productlabel')->__('Position'),
            'name' => 'position',
            'required' => true,
            'values' => Mage::getSingleton('productlabel/position')->getOptionHash(),
            'disabled' => ($inStore && !$data['position_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="position_default" name="position_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['position_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="position_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
        ));
        $fieldset2->addField('image', 'image', array(
            'name' => 'image',
            'label' => Mage::helper('productlabel')->__('Image'),
            'required' => false,
        ));
        $fieldset2->addField('text', 'textarea', array(
            'label' => Mage::helper('productlabel')->__('Text'),
            'required' => false,
            'name' => 'text',
			'style' => 'height: 60px;',
            'disabled' => ($inStore && !$data['text_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="text_default" name="text_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['text_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="text_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
        ));
        $fieldset2->addField('variables', 'hidden', array(
            'name' => 'variables',
            'value' => Zend_Json::encode($this->getVariables())
        ));
        $data['variables'] = Zend_Json::encode($this->getVariables());
        $insertVariableButton = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('adminhtml')->__('Insert Variable...'),
            'onclick' => 'templateControl.openVariableChooser();return false;'
        ));

        $fieldset2->addField('insert_variable', 'note', array(
            'text' => $insertVariableButton->toHtml(),
            'note' => Mage::helper('productlabel')->__('You can use predefined values in this field. Please refer to the extension’s user guide. ')
        ));


        //        Category Page Label
        $fieldset3 = $form->addFieldset('productlabel_category_page', array(
            'legend' => Mage::helper('productlabel')->__('Label on Category Page')
        ));
        if ($data['same_on_two_page'] == null) {
            $data['same_on_two_page'] = 1;
        }
        $fieldset3->addField('same_on_two_page', 'select', array(
            'label' => Mage::helper('productlabel')->__('Use settings of product page'),
            'name' => 'same_on_two_page',
            'values' => array(0 => 'No', 1 => 'Yes'),
            'onchange' => 'hidesetting()',
            'after_element_html' => '
				  <script type="text/javascript">
				  function hidesetting(){
					if($("same_on_two_page").value == 1){
						$("insert_variable_category").parentElement.parentElement.hide();
						$("category_image").parentElement.parentElement.hide();
						$("category_text").parentElement.parentElement.hide();
						$("category_position").parentElement.parentElement.hide();
						$("category_display").parentElement.parentElement.hide();
						}
					else {
						$("insert_variable_category").parentElement.parentElement.show();
						$("category_image").parentElement.parentElement.show();
						$("category_text").parentElement.parentElement.show();
						$("category_position").parentElement.parentElement.show();
						$("category_display").parentElement.parentElement.show();
						}
				  }
				  </script>',
        ));
        $fieldset3->addField('category_display', 'select', array(
            'label' => Mage::helper('productlabel')->__('Display'),
            'name' => 'category_display',
            'required' => true,
            'values' => array(0 => 'No', 1 => 'Yes'),
            'disabled' => ($inStore && !$data['category_display_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="category_display_default" name="category_display_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['category_display_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="category_display_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
        ));

        $fieldset3->addField('category_position', 'select', array(
            'label' => Mage::helper('productlabel')->__('Position'),
            'name' => 'category_position',
            'required' => true,
            'values' => Mage::getSingleton('productlabel/position')->getOptionHash(),
            'disabled' => ($inStore && !$data['category_position_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="category_position_default" name="category_position_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['category_position_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="category_position_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
        ));
        $fieldset3->addField('category_image', 'image', array(
            'name' => 'category_image',
            'label' => Mage::helper('productlabel')->__('Image'),
            'required' => false,
        ));
        $fieldset3->addField('category_text', 'textarea', array(
            'label' => Mage::helper('productlabel')->__('Text'),
            'required' => false,
            'name' => 'category_text',
			'style' => 'height: 60px;',
            'disabled' => ($inStore && !$data['category_text_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="category_text_default" name="category_text_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['category_text_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="category_text_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
        ));
        $insertVariableButton2 = $this->getLayout()->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('adminhtml')->__('Insert Variable...'),
            'onclick' => 'templateControl.openCategoryVariableChooser();return false;'
        ));

        $fieldset3->addField('insert_variable_category', 'note', array(
            'text' => $insertVariableButton2->toHtml(),
            'note' => Mage::helper('productlabel')->__('You can use predefined values in this field. Please refer to the extension’s user guide. '),
            'after_element_html' => '<script type="text/javascript">			
				var templateControl = {
						variables: null,
						openVariableChooser: function() {
						Variables.init("text");
						if (this.variables == null) {
						Variables.resetData();
						this.variables = $("variables").value.evalJSON();
						}
						if (this.variables) {
							Variables.openVariableChooser(this.variables);
						}
						},
						openCategoryVariableChooser: function() {
						Variables.init("category_text");
						if (this.variables == null) {
						Variables.resetData();
						this.variables = $("variables").value.evalJSON();
						}
						if (this.variables) {
							Variables.openVariableChooser(this.variables);
						}
						}				
					};
				
				if($("same_on_two_page").value == 1)
				{
						$("category_display").parentElement.parentElement.hide();
						$("insert_variable_category").parentElement.parentElement.hide();
						$("category_image").parentElement.parentElement.hide();
						$("category_text").parentElement.parentElement.hide();
						$("category_position").parentElement.parentElement.hide();
				}
			</script>'
        ));
        if (isset($data['image']) && $data['image']) {
            $data['image'] = Mage::getBaseUrl('media') . 'productlabel/label/' . $data['image'];
        }
        if (isset($data['category_image']) && $data['category_image']) {
            $data['category_image'] = Mage::getBaseUrl('media') . 'productlabel/label/' . $data['category_image'];
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }

    public function getVariables() {
        $variables = array();
        $variables[] = Mage::getModel('productlabel/productvariables')->getProductVariablesOptionArray(true);
        return $variables;
    }

}