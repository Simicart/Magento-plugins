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
        Mage::helper('simiapicache')->flushCache();
        Mage::getSingleton('adminhtml/session')
            ->addSuccess(Mage::helper('simiapicache')->__('Api Cache has been Flushed.'));
        $this->_redirect('adminhtml/system_config/edit/section/simiapicache');
        return;
    }

}