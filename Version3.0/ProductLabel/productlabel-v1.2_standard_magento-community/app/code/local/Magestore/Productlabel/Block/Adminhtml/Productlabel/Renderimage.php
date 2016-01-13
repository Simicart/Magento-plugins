<?php

class Magestore_Productlabel_Block_Adminhtml_Productlabel_Renderimage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        if ($image = $row->getData($this->getColumn()->getIndex())) {
            return '<img src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'productlabel/label/' . $image . ' " width="20 px" height="20px" />';
        } else {
            return null;
        }
    }

}