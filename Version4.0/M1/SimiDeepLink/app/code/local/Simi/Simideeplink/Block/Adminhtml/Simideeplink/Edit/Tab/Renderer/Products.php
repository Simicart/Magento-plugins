<?php

class Simi_Simideeplink_Block_Adminhtml_Simideeplink_Edit_Tab_Renderer_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $checked = '';
        if (in_array($row->getId(), $this->_getSelectedDevices()))
            $checked = 'checked';
        $html = '<input type="checkbox" ' . $checked . ' name="selected" value="' . $row->getId() . '" class="checkbox" onclick="selectProduct(this)">';
        return sprintf('%s', $html);
    }

    protected function _getSelectedDevices() {
        $devices = $this->getRequest()->getPost('selected', array());
        if (!$devices) {
            if ($this->getRequest()->getParam('selected_ids')) {
                $devices = explode(',', $this->getRequest()->getParam('selected_ids'));
            }
        }
        return $devices;
    }

}
