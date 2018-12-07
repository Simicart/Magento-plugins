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
        $actionHtml = '';
        $buildButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id' => 'flush_api_cache',
                    'label' => __('Flush'),
                    'onclick' => 'flushApi()',
                    'style' => 'margin-left : 10px'
                )
            );
        $actionHtml .= $buildButton->toHtml();

        $actionHtml .= '<script type="text/javascript">
                function flushApi(){
                    var select_api_cache = document.getElementById("simiapicache_apicache_model_api");
                    var val = select_api_cache.getValue().join();
                    var url = "'.Mage::helper('adminhtml')->getUrl('adminhtml/simiapicache_index/flush').'"
                    url = url + "?api="+val
                    setLocation(url)
                }
                
            </script>';
        return $actionHtml;
    }
}