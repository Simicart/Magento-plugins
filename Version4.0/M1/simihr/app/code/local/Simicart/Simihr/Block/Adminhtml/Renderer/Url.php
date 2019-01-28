<?php
class Simicart_Simihr_Block_Adminhtml_Renderer_Url extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $cvUrl = $row->getData($this->getColumn()->getIndex());
       if($cvUrl != '') {
           $src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'simihr/submissions/'.$cvUrl;
           return "<a href='".$src."'>Download</a>";
       }
    }
}
