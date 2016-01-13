<?php

class Magestore_Productlabel_Block_Adminhtml_Productlabel_Renderapplied extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        if ($applied = $row->getData($this->getColumn()->getIndex())) {
            if ($applied==1)
                return '<span class="grid-severity-notice"><span>Ready</span></span>';
            else {
                return '<span class="grid-severity-critical"><span>Not Ready</span></span>';
            }
        } else {
            return '--';
        }
    }

}