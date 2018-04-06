<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 11/22/17
 * Time: 11:11 AM
 */
class Simi_Simiapicache_Adminhtml_Simiapicache_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simiapicache');
    }
    
    public function flushAction() {
        die('a');
    }

}