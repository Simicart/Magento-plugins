<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simifbconnect
 * @copyright   Copyright (c) 2012
 * @license     
 */

/**
 * Simifbconnect Helper
 * 
 * @category    
 * @package     Simifbconnect
 * @author      Developer
 */
class Simi_Simifbconnect_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function checkIfHasChild($category)
    {
        $categoryChildrenCount = $category->getChildrenCount();
        if ($categoryChildrenCount > 0)
            $categoryChildrenCount = 1;
        else
            $categoryChildrenCount = 0;
        if (!$categoryChildrenCount) {
            return '0';
        }

        return '1';
    }
}
