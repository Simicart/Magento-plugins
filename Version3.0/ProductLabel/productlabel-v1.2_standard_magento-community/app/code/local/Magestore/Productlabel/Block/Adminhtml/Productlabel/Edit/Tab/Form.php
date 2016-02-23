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
 * Productlabel Edit Form Content Tab Block
 * 
 * @category    
 * @package     Productlabel
 * @author      Developer
 */
class Magestore_Productlabel_Block_Adminhtml_Productlabel_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_Productlabel_Block_Adminhtml_Productlabel_Edit_Tab_Form
     */
    protected function _prepareLayout() {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addItem('js', 'prototype/window.js')
                    ->addItem('js', 'mage/adminhtml/variables.js');
        }
        return parent::_prepareLayout();
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $dataObj = new Varien_Object(array(
            'store_id' => '',
            'status_in_store',
            'display_in_store',
            'position_in_store',
            'text_in_store',
            'category_display_in_store',
            'category_position_in_store',
            'category_text_in_store'
        ));
        if (Mage::getSingleton('adminhtml/session')->getProductlabelData()) {
            $data = Mage::getSingleton('adminhtml/session')->getProductlabelData();
            Mage::getSingleton('adminhtml/session')->setProductlabelData(null);
        } elseif (Mage::registry('productlabel_data')) {
            $data = Mage::registry('productlabel_data')->getData();
        }

        if (isset($data))
            $dataObj->addData($data);
        $data = $dataObj->getData();

        $storeId = $this->getRequest()->getParam('store');
        if ($storeId)
            $store = Mage::getModel('core/store')->load($storeId);
        else
            $store = Mage::app()->getStore();
        
        $fieldset = $form->addFieldset('productlabel_form', array(
            'legend' => Mage::helper('productlabel')->__('General Information')
        ));

        $inStore = $this->getRequest()->getParam('store');
//        Zend_Debug::dump($inStore);
//        die();
        $defaultLabel = Mage::helper('productlabel')->__('Use Default');
        $defaultTitle = Mage::helper('productlabel')->__('-- Please Select --');
        $scopeLabel = Mage::helper('productlabel')->__('STORE VIEW');

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('productlabel')->__('Label Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('productlabel')->__('Description'),
            'title' => Mage::helper('productlabel')->__('Description'),
            'style' => 'height: 100px;',
            'wysiwyg' => false,
            'required' => false,
        ));
        if ($data['status'] == null) {
            $data['status'] = 2;
        }
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('productlabel')->__('Status'),
            'name' => 'status',
            'values' => Mage::getSingleton('productlabel/status')->getOptionHash(),
            'disabled' => ($inStore && !$data['status_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="status_default" name="status_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['status_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="status_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
        ));
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name' => 'from_date',
            'label' => Mage::helper('productlabel')->__('Start Date'),
            'title' => Mage::helper('productlabel')->__('Start Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format' => $dateFormatIso
        ));
        $fieldset->addField('to_date', 'date', array(
            'name' => 'to_date',
            'label' => Mage::helper('productlabel')->__('End Date'),
            'title' => Mage::helper('productlabel')->__('End Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format' => $dateFormatIso
        ));

        $fieldset->addField('priority', 'text', array(
            'name' => 'priority',
            'class' => 'validate-number',
            'label' => Mage::helper('productlabel')->__('Priority'),
            'note' => Mage::helper('productlabel')->__('The higher the value, the higher the priority.'),
        ));

//        Product Page Label
        $fieldset2 = $form->addFieldset('productlabel_product_page', array(
            'legend' => Mage::helper('productlabel')->__('Label on Product Page')
        ));

        $fieldset2->addField('display', 'select', array(
            'label' => Mage::helper('productlabel')->__('Display'),
            'name' => 'display',
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

        $fieldset2->addField('position', 'select', array(
            'label' => Mage::helper('productlabel')->__('Position'),
            'name' => 'position',
            'required' => false,
            'values' => Mage::getModel('productlabel/position')->getOptionHash(),
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
        if ($data['is_auto_fill'] == null) {
            $data['is_auto_fill'] = 1;
        }

        $fieldset3->addField('is_auto_fill', 'select', array(
            'label' => Mage::helper('productlabel')->__('Use settings of product page'),
            'name' => 'is_auto_fill',
            'values' => array(0 => 'No', 1 => 'Yes'),
            'onchange' => 'hidesetting(this)',
        ));
        $fieldset3->addField('category_display', 'select', array(
            'label' => Mage::helper('productlabel')->__('Display'),
            'name' => 'category_display',
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
            'required' => false,
            'values' => Mage::getModel('productlabel/position')->getOptionHash(),
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
        $insertVariableButton2 = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('adminhtml')->__('Insert Variable...'),
            'onclick' => 'templateControl.openCategoryVariableChooser();return false;'
        ));

        $fieldset3->addField('insert_variable_category', 'note', array(
            'text' => $insertVariableButton2->toHtml(),
            'note' => Mage::helper('productlabel')->__('You can use predefined values in this field. Please refer to the extension’s user guide. '),
        ));
        if (isset($data['image']) && $data['image']) {
            $dir_img = Mage::getBaseDir('media') . DS . 'productlabel' . DS . 'label' . DS . $data['image'];
            if (file_exists($dir_img))
            $data['image'] = Mage::getBaseUrl('media') . 'productlabel/label/' . $data['image'];
            else
                $data['image'] = '';
        }
        if (isset($data['category_image']) && $data['category_image']) {
            $dir_img_cate = Mage::getBaseDir('media') . DS . 'productlabel' . DS . 'label' . DS . $data['category_image'];

            if (file_exists($dir_img_cate))
                $data['category_image'] = Mage::getBaseUrl('media') . 'productlabel/label/' . $data['category_image'];
            else
                $data['category_image'] = '';
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