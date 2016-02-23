<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category  
 * @package   Simivideo
 * @copyright   Copyright (c) 2012 
 * @license   
 */

/**
 * Simi Resource Model
 * 
 * @category  
 * @package   Simivideo
 * @author    Developer
 */
class Simi_Simivideo_Model_Mysql4_Simivideo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simivideo/simivideo', 'video_id');
    }
}