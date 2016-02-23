<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simivideo
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Simi Edit Form Content Tab Block
 * 
 * @category    
 * @package     Simivideo
 * @author      Developer
 */
class Simi_Simivideo_Block_Adminhtml_Simivideo_Edit_Tab_Render extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row) 
	{
        $checked = '';
        if(in_array($row->getId(), $this->_getSelectedProducts()))
                $checked = 'checked';
        $html = '<input type="checkbox" '.$checked.' name="selected" value="'.$row->getId().'" class="checkbox" onclick="selectProduct(this)">';
        return sprintf('%s', $html);
	}
    
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected', array());
        return $products;
    }

}

