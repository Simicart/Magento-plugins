<?php
/**

 */
class Simi_Simitracking_Block_Adminhtml_User extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_user';
        $this->_blockGroup = 'simitracking';
        $this->_headerText = Mage::helper('simitracking')->__('User');
        $this->_addButtonLabel = Mage::helper('simitracking')->__('Add User');
        parent::__construct();
    }

}