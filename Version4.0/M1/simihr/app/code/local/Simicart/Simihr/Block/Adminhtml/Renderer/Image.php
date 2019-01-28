<?php
    class Simicart_Simihr_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    {
        public function render(Varien_Object $row)
        {
            $imgUrl = $row->getData($this->getColumn()->getIndex());
            if($imgUrl != '') {
                $src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .$imgUrl;
                return "<img src='".$src."' width='100px'/>";
            }

        }
    }
?>