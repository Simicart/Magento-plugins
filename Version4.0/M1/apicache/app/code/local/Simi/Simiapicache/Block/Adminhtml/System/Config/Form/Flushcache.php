<?php

/**
 * Created by PhpStorm.
 * User: scottsimicart
 * Date: 12/12/17
 * Time: 6:14 PM
 */
class Simi_Simiapicache_Block_Adminhtml_System_Config_Form_Flushcache extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->getButtonHtml();
    }
    
    public function getButtonHtml()
    {
        $buildButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id' => 'flush_api_cache',
                    'label' => __('Flush'),
                    'onclick' => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl('adminhtml/simiapicache_index/flush') . '\')',
                    'style' => 'margin-left : 10px'
                )
            );
        return $buildButton->toHtml();
    }
}