<?php
/**

 */
class Simi_Simitracking_Block_Adminhtml_Role extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_role';
        $this->_blockGroup = 'simitracking';
        $this->_headerText = Mage::helper('simitracking')->__('Role');
        $this->_addButtonLabel = Mage::helper('simitracking')->__('Add Role');
        parent::__construct();
    }

}