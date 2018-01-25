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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Actions
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
        protected function _prepareForm() {
        if (Mage::registry('giftvoucher_data')) {
            $model = Mage::registry('giftvoucher_data');
        } else {
            $model = Mage::getModel('simigiftvoucher/giftvoucher');
        }
        $data = $model->getData();
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('giftvoucher_');
        /**
         * action condition
         */
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newActionHtml/form/giftvoucher_actions_fieldset'));
        $fieldset = $form->addFieldset('description_fieldset', array('legend' => Mage::helper('simigiftvoucher')->__('Description')));
        $fieldset->addField('cart_rule_description', 'editor', array(
            'label' => Mage::helper('simigiftvoucher')->__('Describe conditions applied to shopping cart when using this gift code'),
            'title' => Mage::helper('simigiftvoucher')->__('Describe conditions applied to shopping cart when using this gift code'),
            'name' => 'cart_rule_description',
        ));
        $fieldset = $form->addFieldset('actions_fieldset', array('legend' => Mage::helper('simigiftvoucher')->__('Allow using the gift code only if products in cart meet the following conditions (leave blank for all products)')))->setRenderer($renderer);
        $fieldset->addField('actions', 'text', array(
            'label' => Mage::helper('simigiftvoucher')->__('Apply To'),
            'title' => Mage::helper('simigiftvoucher')->__('Apply To'),
            'name' => 'actions',
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
