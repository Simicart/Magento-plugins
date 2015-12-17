<?php

class Magestore_Productlabel_Block_Adminhtml_Productlabel_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');
        $this->setForm($form);
        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
            $model = Mage::getModel('productlabel/productlabel')
                    ->load($data['label_id'])
                    ->setData($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        } elseif (Mage::registry('productlabel_data')) {
            $model = Mage::registry('productlabel_data');
            $data = $model->getData();
        }
        $fieldset = $form->addFieldset('productlabel_select_form', array(
            'legend' => Mage::helper('productlabel')->__('Configure Product Label Condition'),
        ));
        $condition_select = array(
            0 => array(
                'label' => Mage::helper('productlabel')->__('On Sale'),
                'value' => Mage::helper('productlabel')->__('onsale'),
            ),
            1 => array(
                'label' => Mage::helper('productlabel')->__('New'),
                'value' => Mage::helper('productlabel')->__('newproduct')),
            2 => array(
                'label' => Mage::helper('productlabel')->__('Custom'),
                'value' => Mage::helper('productlabel')->__('custom'))
        );
        if(!$data['condition_selected']){
            $data['condition_selected']='onsale';
        }
        $fieldset->addField('condition_selected', 'select', array(
            'label' => Mage::helper('productlabel')->__('Select Condition'),
            'name' => 'condition_selected',
            'values' => $condition_select,
            'required' => false,
            'onchange' => 'modifyTargetElement(this)',
        ));
        $fieldset->addField('threshold', 'text', array(
            'label' => Mage::helper('productlabel')->__('Threshold (%)'),
            'class' => 'validate-number',
            'required' => true,
            'name' => 'threshold',
            'value' => 0,
            'note' => Mage::helper('productlabel')->__('For eg, if 10 is entered in the field, the label will show on all products of discounts equal or greater than 10%.')
        ));
         $fieldset->addField('auto_apply', 'hidden', array(
            'name' => 'auto_apply',
        ));
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset2 = $form->addFieldset('conditions_fieldset', array('legend' => Mage::helper('productlabel')->__('Apply the label only if the following conditions are met (leave blank for all orders)')))->setRenderer($renderer);
        $fieldset2->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('productlabel')->__('Conditions'),
            'title' => Mage::helper('productlabel')->__('Conditions'),
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}