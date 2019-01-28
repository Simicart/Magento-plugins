<?php
class Simicart_Simihr_Block_Adminhtml_Content_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */

    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
    public function __construct()
    {
        $this->_blockGroup = 'simihr';
        $this->_controller = 'adminhtml_content';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Content'));
        $this->_updateButton('delete', 'label', $this->__('Delete Content'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('simicart_simihr')->getId()) {
            return $this->__('Edit Content');
        }
        else {
            return $this->__('New Content');
        }
    }
}