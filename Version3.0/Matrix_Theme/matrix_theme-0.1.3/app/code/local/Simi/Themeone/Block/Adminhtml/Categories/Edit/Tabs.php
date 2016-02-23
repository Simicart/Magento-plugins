<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Themeone
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Themeone Edit Tab Block
 * 
 * @category    
 * @package     Themeone
 * @author      Developer
 */
class Simi_Themeone_Block_Adminhtml_Categories_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('themeone_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('themeone')->__('Category Manager'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Simi_ThemeOne_Block_Adminhtml_Themeone_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('themeone')->__('Category Manager'),
            'title'     => Mage::helper('themeone')->__('Category Manager'),
            'content'   => $this->getLayout()
                                ->createBlock('themeone/adminhtml_categories_edit_tab_form')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}